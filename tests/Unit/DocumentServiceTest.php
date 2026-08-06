<?php

namespace Tests\Unit;

use App\Models\Document;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * ทดสอบการสกัด/ค้นเอกสาร — ใช้ Storage::fake('local') + RefreshDatabase ใน Unit suite
 * (Feature suite ใช้ MySQL จริง ห้าม RefreshDatabase — แต่ unit นี้แยกอยู่ tests/Unit
 * ต้องรันด้วย connection sqlite ตาม memory note)
 */
class DocumentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_store_upload_creates_row_and_extracts_text_from_image(): void
    {
        // ปิด OCR — deterministic (ไม่พึ่ง tesseract/ภาพใน CI)
        config(['ai-chat.documents.ocr_mode' => 'off']);

        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('tag.png', 2, 2);
        $doc = app(DocumentService::class)->storeUpload($file, $user);

        $this->assertInstanceOf(Document::class, $doc);
        $this->assertSame(Document::KIND_IMAGE, $doc->kind);
        $this->assertTrue(Storage::disk('local')->exists($doc->storage_path));
        $this->assertSame(Document::STATUS_READY, $doc->status);
        $this->assertEmpty($doc->extracted_text);
    }

    public function test_documentContent_filters_chunks_by_query(): void
    {
        $doc = Document::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'สัญญาเช่า',
            'original_filename' => 'contract.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size_bytes' => 100,
            'storage_path' => 'documents/2026-08-06/test.pdf',
            'kind' => Document::KIND_PDF,
            'status' => Document::STATUS_READY,
            'extracted_text' => 'ย่อหน้าแรกเกี่ยวกับค่าเช่าของเครื่องซักผ้า',
            'chunks_json' => [
                ['text' => 'ย่อหน้าแรก: สัญญาเช่าเครื่องซักผ้า uperdate 2026'],
                ['text' => 'ย่อหน้าที่สอง: ราคาค่าเช่าต่อเดือน'],
            ],
        ]);

        $svc = app(DocumentService::class);
        $result = $svc->documentContent($doc->id, 'ค่าเช่า');

        $this->assertStringContainsString('ค่าเช่า', $result);
        $this->assertStringNotContainsString('uperdate', $result); // chunk ที่ไม่ตรงถูกกรอง
    }

    public function test_chunking_does_not_infinite_loop_when_overlap_larger_than_chunk(): void
    {
        // regression: overlap > chunk size เดิมทำให้ start ถอยหลังไม่จบ → memory exhaust
        config([
            'ai-chat.documents.chunk_size_chars' => 100,
            'ai-chat.documents.chunk_overlap_chars' => 250,
        ]);

        $svc = app(DocumentService::class);

        $ref = new \ReflectionMethod($svc, 'chunkText');
        $ref->setAccessible(true);
        $chunks = $ref->invoke($svc, str_repeat('ก', 500));

        $this->assertNotEmpty($chunks);
        $this->assertLessThan(500, count($chunks));

        // ตรวจ coverage: ข้อความครบทุกตัว (overlap ทำให้ซ้ำได้ แต่ห้ามหาย)
        $joined = implode('', array_column($chunks, 'text'));
        $this->assertStringContainsString(str_repeat('ก', 500), $joined);
    }

    public function test_docx_extracts_paragraph_text(): void
    {
        // สร้าง .docx จริง: ZIP + word/document.xml (w:p/w:t)
        $zip = new \ZipArchive();
        $docxPath = storage_path('app/test.docx');
        if ($zip->open($docxPath, \ZipArchive::CREATE) !== true) {
            $this->fail('cannot create zip');
        }
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/></Types>');
        $zip->addFromString('word/document.xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            . '<w:body><w:p><w:r><w:t>สัญญาเช่าเครื่องซักผ้า</w:t></w:r></w:p>'
            . '<w:p><w:r><w:t>ค่าเช่าเดือนละ 5000 บาท</w:t></w:r></w:p>'
            . '</w:body></w:document>'
        );
        $zip->close();

        $file = new UploadedFile($docxPath, 'test.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', null, true);
        $user = User::factory()->create();

        $doc = app(DocumentService::class)->storeUpload($file, $user);

        $this->assertSame(Document::STATUS_READY, $doc->status);
        $this->assertSame(Document::KIND_OFFICE, $doc->kind);
        $this->assertStringContainsString('สัญญาเช่าเครื่องซักผ้า', (string) $doc->extracted_text);
        $this->assertStringContainsString('5000 บาท', (string) $doc->extracted_text);

        unlink($docxPath);
    }

    public function test_txt_extracts_content(): void
    {
        $txtPath = storage_path('app/test.txt');
        file_put_contents($txtPath, "สัญญาเช่าเครื่องจักร\nระยะเวลา 12 เดือน");

        $file = new UploadedFile($txtPath, 'test.txt', 'text/plain', null, true);
        $user = User::factory()->create();

        $doc = app(DocumentService::class)->storeUpload($file, $user);

        $this->assertSame(Document::STATUS_READY, $doc->status);
        $this->assertStringContainsString('สัญญาเช่าเครื่องจักร', (string) $doc->extracted_text);

        unlink($txtPath);
    }

    public function test_legacy_doc_fails_with_clear_reason(): void
    {
        $docPath = storage_path('app/test.doc');
        file_put_contents($docPath, "\xD0\xCF\x11\xE0 binary old doc"); // OLE header

        $file = new UploadedFile($docPath, 'test.doc', 'application/msword', null, true);
        $user = User::factory()->create();

        $doc = app(DocumentService::class)->storeUpload($file, $user);

        $this->assertSame(Document::STATUS_FAILED, $doc->status);
        $this->assertStringContainsString('ยังไม่รองรับ', (string) $doc->fail_reason);
        $this->assertNull($doc->extracted_text);

        unlink($docPath);
    }

    public function test_deleteDocument_removes_file_and_row(): void
    {
        Storage::disk('local')->put('documents/2026-01-01/x.pdf', 'pdf bytes');

        $doc = Document::create([
            'user_id' => 1,
            'title' => 'x',
            'original_filename' => 'x.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size_bytes' => 8,
            'storage_path' => 'documents/2026-01-01/x.pdf',
            'kind' => 'pdf',
            'status' => 'ready',
        ]);

        $user = new User();
        $user->id = 1;

        $ok = app(DocumentService::class)->deleteDocument($doc->id, $user);

        $this->assertTrue($ok);
        $this->assertDatabaseMissing('documents', ['id' => $doc->id]);
        $this->assertFalse(Storage::disk('local')->exists('documents/2026-01-01/x.pdf'));
    }
}
<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * ทดสอบ Documents flow (staff เท่านั้น) — ใช้ DatabaseTransactions (rollback หลังเทสต์)
 * เพราะ phpunit.xml ชี้ MySQL จริง — ห้าม RefreshDatabase เด็ดขาด
 * ต้องรัน migrate ก่อน (ตาราง documents ต้องมีแล้ว)
 */
class DocumentApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        config(['ai-chat.documents.ocr_mode' => 'off']);
    }

    protected function staffUser(): User
    {
        return User::factory()->create([
            'is_can_access_supervisor' => true,
        ]);
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get('/documents')->assertRedirect(route('login'));
    }

    public function test_worker_cannot_access_documents_page(): void
    {
        $worker = User::factory()->create([
            'is_can_access_worker' => true,
            'is_can_access_supervisor' => false,
            'is_can_access_manager' => false,
            'is_can_access_admin' => false,
        ]);

        // IsStaff redirects to /home (ไม่ใช่ 403) สำหรับ web request
        $this->actingAs($worker)
            ->get('/documents')
            ->assertRedirect('home');
    }

    public function test_staff_can_upload_and_see_document_in_list(): void
    {
        $user = $this->staffUser();

        $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

        $this->actingAs($user)
            ->post('/documents', ['document' => $file])
            ->assertRedirect(route('documents.index'));

        $this->assertDatabaseHas('documents', [
            'original_filename' => $file->getClientOriginalName(),
            'kind' => 'pdf',
        ]);
    }

    public function test_upload_rejects_oversize_file(): void
    {
        config(['ai-chat.documents.max_file_size_mb' => 1]); // 1 MB cap

        $user = $this->staffUser();
        $oversize = UploadedFile::fake()->create('big.pdf', 2048, 'application/pdf'); // 2 MB

        $this->actingAs($user)
            ->post('/documents', ['document' => $oversize])
            ->assertSessionHasErrors('document');
    }

    public function test_search_documents_tool_finds_by_content(): void
    {
        $user = $this->staffUser();
        $svc = app(DocumentService::class);

        $doc = Document::create([
            'user_id' => $user->id,
            'title' => 'สัญญาเช่า',
            'original_filename' => 'contract.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size_bytes' => 100,
            'storage_path' => 'documents/x.pdf',
            'kind' => 'pdf',
            'status' => Document::STATUS_READY,
            'extracted_text' => 'ข้อความเกี่ยวกับเครื่องซักผ้ายี่ห้อแฮกเกอร์ราคา 20000 บาท',
            'chunks_json' => [['text' => 'ข้อความเกี่ยวกับเครื่องซักผ้า']],
        ]);

        $results = $svc->searchDocuments('ซักผ้า');
        $this->assertCount(1, $results);
        $this->assertSame($doc->id, $results[0]['id']);
    }

    public function test_destroy_removes_file_and_row(): void
    {
        $user = $this->staffUser();

        Storage::disk('local')->put('documents/2026-01-01/x.pdf', 'pdf');

        $doc = Document::create([
            'user_id' => $user->id,
            'title' => 'x',
            'original_filename' => 'x.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size_bytes' => 3,
            'storage_path' => 'documents/2026-01-01/x.pdf',
            'kind' => 'pdf',
            'status' => 'ready',
        ]);

        $this->actingAs($user)
            ->delete(route('documents.destroy', $doc))
            ->assertRedirect(route('documents.index'));

        $this->assertDatabaseMissing('documents', ['id' => $doc->id]);
        $this->assertFalse(Storage::disk('local')->exists('documents/2026-01-01/x.pdf'));
    }
}
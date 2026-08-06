<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * เอกสารที่ staff อัปโหลด — เก็บไฟล์ + สกัดเนื้อหา (OCR tesseract / pdftotext)
 * ให้ AI agent (search_documents / get_document_content / analyze_document_image) ใช้ตอบ
 *
 * ไม่ throw เด็ดขาด — ความผิดพลาดระหว่างสกัด → status=failed + fail_reason + Log (LLM เป็น optional philosophy เดียวกัน)
 */
class DocumentService
{
    private const SEARCH_LIMIT = 50;
    private const RESULT_LIMIT = 5;

    public function __construct()
    {
    }

    /**
     * บันทึกไฟล์ + สร้าง row + สกัดเนื้อหา (sync ใน request — queue เป็น sync)
     */
    public function storeUpload(UploadedFile $file, User $user): Document
    {
        set_time_limit(180);

        $extension = strtolower((string) ($file->getClientOriginalExtension() ?: $file->extension()));
        $kind = $this->kindForExtension($extension);

        $disk = Storage::disk($this->disk());
        $date = now()->format('Y-m-d');
        $storedName = uniqid('doc_') . '.' . $extension;
        $path = $disk->putFileAs(
            $this->dir() . '/' . $date,
            $file,
            $storedName
        );

        if ($path === false) {
            throw new \RuntimeException('ไม่สามารถบันทึกไฟล์ลง storage ได้');
        }

        $document = Document::create([
            'user_id' => $user->id,
            'title' => $this->titleFromFilename($file->getClientOriginalName()),
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => (string) $file->getMimeType(),
            'extension' => $extension,
            'size_bytes' => $file->getSize(),
            'storage_path' => $path,
            'kind' => $kind,
            'status' => Document::STATUS_PENDING,
        ]);

        try {
            $this->process($document);
        } catch (\Throwable $e) {
            Log::warning('DocumentService process failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            $document->status = Document::STATUS_FAILED;
            $document->fail_reason = mb_substr($e->getMessage(), 0, 490);
            $document->save();
        }

        return $document;
    }

    /**
     * สกัดเนื้อหาตามชนิดไฟล์ → อัปเดต row (pending → ready|failed)
     */
    public function process(Document $document): void
    {
        if ($document->status === Document::STATUS_READY) {
            return;
        }

        $absPath = Storage::disk($this->disk())->path($document->storage_path);
        if (!is_file($absPath)) {
            $this->fail($document, 'ไม่พบไฟล์ใน storage');
            return;
        }

        $text = match ($document->kind) {
            Document::KIND_IMAGE => $this->ocrImage($absPath),
            Document::KIND_PDF => $this->extractPdf($absPath),
            default => $this->extractOffice($document, $absPath),
        };

        $text = $this->sanitizeText($text);

        if (mb_strlen($text) > 0) {
            $chunks = $this->chunkText($text);
            $document->extracted_text = $text;
            $document->chunks_json = $chunks;
            $document->extracted_chars = mb_strlen($text);
        }

        $document->status = Document::STATUS_READY;
        $document->fail_reason = null;
        $document->save();
    }

    /**
     * ภาพ → tesseract OCR (tha+eng) — สกัดข้อความจากรูปถ่าย/สแกน
     */
    protected function ocrImage(string $absPath): string
    {
        $tesseract = $this->binary('tesseract');
        if ($tesseract === null || !$this->tesseractHasThai()) {
            return '';
        }

        $tmpDir = sys_get_temp_dir() . '/linin_ocr_' . uniqid();
        mkdir($tmpDir, 0700);

        try {
            $out = $tmpDir . '/out';
            $cmd = escapeshellarg($tesseract)
                . ' ' . escapeshellarg($absPath)
                . ' ' . escapeshellarg($out)
                . ' -l tha+eng --psm 3 2>&1';

            $this->runShell($cmd, $exitCode);

            $result = $out . '.txt';
            if ($exitCode !== 0 || !is_file($result)) {
                return '';
            }

            return (string) file_get_contents($result);
        } finally {
            $this->removeDir($tmpDir);
        }
    }

    /**
     * PDF → pdftotext (searchable) / pdftoppm → tesseract (สแกน) — เอาอันที่ได้ text ก่อน
     */
    protected function extractPdf(string $absPath): string
    {
        // 1) PDF มี text layer อยู่แล้ว → pdftotext ตรงๆ
        $pdftotext = $this->binary('pdftotext');
        if ($pdftotext !== null) {
            $tmpDir = sys_get_temp_dir() . '/linin_pdf_' . uniqid();
            mkdir($tmpDir, 0700);
            try {
                $out = $tmpDir . '/out.txt';
                $cmd = escapeshellarg($pdftotext)
                    . ' ' . escapeshellarg($absPath)
                    . ' ' . escapeshellarg($out)
                    . ' 2>&1';
                $this->runShell($cmd, $exitCode);

                if ($exitCode === 0 && is_file($out)) {
                    $text = (string) file_get_contents($out);
                    if (trim($text) !== '') {
                        return $text;
                    }
                }
            } finally {
                $this->removeDir($tmpDir);
            }
        }

        // 2) สแกน/ไม่มี text layer → แปลงหน้าแรกๆ เป็นภาพ แล้ว tesseract
        $pdftoppm = $this->binary('pdftoppm');
        if ($pdftoppm !== null) {
            $tmpDir = sys_get_temp_dir() . '/linin_pdf_' . uniqid();
            mkdir($tmpDir, 0700);
            try {
                $cmd = escapeshellarg($pdftoppm)
                    . ' -jpeg -r 150 -f 1 -l ' . (int) config('ai-chat.documents.max_pdf_pages', 30)
                    . ' ' . escapeshellarg($absPath)
                    . ' ' . escapeshellarg($tmpDir . '/page')
                    . ' 2>&1';
                $this->runShell($cmd, $exitCode);

                if ($exitCode !== 0) {
                    return '';
                }

                $text = '';
                foreach (glob($tmpDir . '/page-*.jpg') ?: [] as $img) {
                    $pageText = $this->ocrImage($img);
                    if ($pageText !== '') {
                        $text .= "\n\n--- หน้า " . ((int) basename($img, '.jpg')) . " ---\n\n" . $pageText;
                    }
                }
                return $text;
            } finally {
                $this->removeDir($tmpDir);
            }
        }

        return '';
    }

    /**
     * office (docx/xlsx/txt...) — เฟส 1 ไม่รองรับการอ่าน ส่ง fail_reason ชัดเจน
     */
    protected function extractOffice(Document $document, string $absPath): string
    {
        $document->fail_reason = 'ยังไม่รองรับการอ่านไฟล์ .' . $document->extension . ' ในเฟสนี้ — ไฟล์ถูกเก็บไว้แล้ว';
        return '';
    }

    // ── ช่วยค้นหาของ AI agent ─────────────────────────────────────────────

    /**
     * ค้นเอกสาร (เฉพาะ status=ready) จาก title/ชื่อไฟล์/เนื้อหา — ใช้ ranking แบบง่าย
     * (ไม่มี vector DB; เอกสารน้อย — LIKE ตาม title > filename > text)
     */
    public function searchDocuments(string $query): array
    {
        $terms = array_values(array_filter(
            array_map('trim', preg_split('/[\s,]+/u', $query) ?: []),
            fn ($t) => mb_strlen($t) > 0
        ));

        if (empty($terms)) {
            return [];
        }

        $docs = Document::ready()
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->where(function ($qq) use ($term) {
                        $qq->where('title', 'like', "%{$term}%")
                            ->orWhere('original_filename', 'like', "%{$term}%")
                            ->orWhere('extracted_text', 'like', "%{$term}%");
                    });
                }
            })
            ->latest('id')
            ->limit(self::SEARCH_LIMIT)
            ->get();

        $results = [];
        foreach ($docs as $doc) {
            $score = $this->score($doc, $terms);
            if ($score <= 0) {
                continue;
            }

            $results[] = [
                'id' => $doc->id,
                'title' => $doc->title,
                'filename' => $doc->original_filename,
                'kind' => $doc->kind,
                'score' => $score,
                'preview' => $this->preview($doc, $terms),
            ];
        }

        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($results, 0, self::RESULT_LIMIT);
    }

    /**
     * เนื้อหาเอกสาร — เฉพาะ chunk ที่ตรงคำค้น (กันส่ง text ทั้งหมดยักษ์ไปให้ LLM)
     */
    public function documentContent(int $id, ?string $query = null): string
    {
        $doc = Document::ready()->find($id);
        if (!$doc) {
            return "ไม่พบเอกสาร id {$id} (หรือยังประมวลผลไม่เสร็จ)";
        }

        $header = "เอกสาร #{$doc->id}: {$doc->title} ({$doc->original_filename})";
        if (empty($doc->extracted_text)) {
            return $header . "\n(ไม่มีเนื้อหาที่สกัดได้ — ไฟล์นี้เป็นรูปที่ไม่มีตัวอักษรหรือยังไม่รองรับการอ่าน)";
        }

        $chunks = $doc->chunks($query);
        $text = $header;

        foreach (array_slice($chunks, 0, 5) as $i => $chunk) {
            $text .= "\n\n--- ตอนที่ " . ($i + 1) . " ---\n" . trim((string) ($chunk['text'] ?? ''));
        }

        return $text;
    }

    public function deleteDocument(int $id, User $actor): bool
    {
        $doc = Document::find($id);
        if (!$doc) {
            return false;
        }

        try {
            Storage::disk($this->disk())->delete($doc->storage_path);
        } catch (\Throwable $e) {
            Log::warning('Document delete file failed', ['document_id' => $id, 'error' => $e->getMessage()]);
        }

        $doc->delete();

        Log::info('Document deleted', ['document_id' => $id, 'actor' => $actor->id]);

        return true;
    }

    // ── internals ─────────────────────────────────────────────────────────

    protected function disk(): string
    {
        return (string) config('ai-chat.documents.storage_disk', 'local');
    }

    protected function dir(): string
    {
        return (string) config('ai-chat.documents.storage_dir', 'documents');
    }

    protected function kindForExtension(string $ext): string
    {
        $allowed = config('ai-chat.documents.allowed_extensions', []);

        foreach (['image', 'pdf', 'office'] as $kind) {
            if (in_array($ext, $allowed[$kind] ?? [], true)) {
                return $kind;
            }
        }

        return Document::KIND_OFFICE;
    }

    protected function titleFromFilename(string $name): string
    {
        $title = pathinfo($name, PATHINFO_FILENAME);
        $title = str_replace(['_', '-'], ' ', $title);
        return mb_substr($title, 0, 255);
    }

    protected function sanitizeText(string $text): string
    {
        $text = preg_replace('/\r\n?/', "\n", $text) ?? $text;
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\n{3,}/', "\n\n", $text) ?? $text;

        $max = (int) config('ai-chat.documents.max_extracted_chars', 50000);
        if (mb_strlen($text) > $max) {
            $text = mb_substr($text, 0, $max);
        }

        return trim($text);
    }

    protected function chunkText(string $text): array
    {
        $size = (int) config('ai-chat.documents.chunk_size_chars', 2000);
        $overlap = (int) config('ai-chat.documents.chunk_overlap_chars', 200);
        $len = mb_strlen($text);

        $chunks = [];
        $start = 0;
        while ($start < $len) {
            $end = min($start + $size, $len);
            $piece = mb_substr($text, $start, $end - $start);

            // ตัดที่ขอบบรรทัดให้ chunk ไม่ขาดกลางประโยค
            if ($end < $len) {
                $lastNewline = mb_strrpos($piece, "\n");
                if ($lastNewline !== false && $lastNewline > $size * 0.6) {
                    $end = $start + $lastNewline;
                    $piece = mb_substr($text, $start, $lastNewline);
                }
            }

            $chunks[] = ['text' => $piece];

            if ($end >= $len) {
                break; // จบข้อความ — กัน infinite loop เมื่อ overlap ใหญ่กว่า chunk
            }

            $start = max($end - $overlap, $start + 1);
        }

        return $chunks;
    }

    protected function score(Document $doc, array $terms): int
    {
        $title = mb_strtolower($doc->title);
        $filename = mb_strtolower($doc->original_filename);
        $text = mb_strtolower((string) $doc->extracted_text);

        $score = 0;
        foreach ($terms as $term) {
            if (mb_strpos($title, $term) !== false) {
                $score += 3;
            }
            if (mb_strpos($filename, $term) !== false) {
                $score += 2;
            }
            if (mb_strpos($text, $term) !== false) {
                $score += 1;
            }
        }
        return $score;
    }

    protected function preview(Document $doc, array $terms): string
    {
        $text = (string) $doc->extracted_text;
        if ($text === '') {
            return '(ไม่มีเนื้อหาที่สกัดได้ — รูป/เอกสารที่อ่านไม่ได้)';
        }

        $lower = mb_strtolower($text);
        $pos = PHP_INT_MAX;
        foreach ($terms as $term) {
            $p = mb_strpos($lower, $term);
            if ($p !== false && $p < $pos) {
                $pos = $p;
            }
        }

        if ($pos === PHP_INT_MAX) {
            $pos = 0;
        }

        $start = max(0, $pos - 100);
        $piece = mb_substr($text, $start, 200);

        return ($start > 0 ? '…' : '') . $piece . '…';
    }

    protected function binary(string $name): ?string
    {
        $cmd = 'command -v ' . escapeshellarg($name) . ' 2>/dev/null';
        $this->runShell($cmd, $exitCode, $out);
        return $exitCode === 0 && !empty($out[0]) ? trim((string) $out[0]) : null;
    }

    protected function tesseractHasThai(): bool
    {
        $tesseract = $this->binary('tesseract');
        if ($tesseract === null) {
            return false;
        }

        $this->runShell(escapeshellarg($tesseract) . ' --list-langs 2>&1', $exitCode, $out);
        if ($exitCode !== 0) {
            return false;
        }

        foreach ($out as $line) {
            if (trim((string) $line) === 'tha') {
                return true;
            }
        }
        return false;
    }

    protected function runShell(string $cmd, ?int &$exitCode = null, ?array &$out = null): void
    {
        $out = [];
        $exitCode = 0;
        $lastLine = '';

        if (function_exists('exec')) {
            exec($cmd, $out, $exitCode);
            return;
        }

        if (function_exists('shell_exec')) {
            $lastLine = (string) shell_exec($cmd);
            if (trim($lastLine) !== '') {
                $out = explode("\n", trim($lastLine));
            }
            $exitCode = 0;
            return;
        }

        // exec/shell_exec โดนปิด (shared hosting) — ถือว่า binary ใช้ไม่ได้
        $exitCode = 127;
        $out = [];
    }

    protected function fail(Document $document, string $reason): void
    {
        $document->status = Document::STATUS_FAILED;
        $document->fail_reason = mb_substr($reason, 0, 490);
        $document->save();
    }

    protected function removeDir(string $dir): void
    {
        foreach (glob($dir . '/*') ?: [] as $f) {
            @unlink($f);
        }
        @rmdir($dir);
    }
}

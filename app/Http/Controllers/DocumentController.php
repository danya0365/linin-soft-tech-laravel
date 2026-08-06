<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

/**
 * เอกสารที่ staff อัปโหลด — ทุก route อยู่ภายใต้ middleware 'staff' (supervisor/manager/admin)
 * path ใน URL เปลี่ยนตาม query ได้ แต่ path ใน storage มาจาก DB row เสมอ (กัน path traversal)
 */
class DocumentController extends Controller
{
    public function __construct(protected DocumentService $documents)
    {
    }

    /**
     * รายการเอกสารทั้งหมด
     */
    public function index()
    {
        $documents = Document::query()
            ->with('user:id,name')
            ->latest('id')
            ->paginate(15);

        return view('documents.index', [
            'documents' => $documents,
            'limits' => $this->limits(),
        ]);
    }

    /**
     * ฟอร์มอัปโหลด
     */
    public function create()
    {
        return view('documents.create', [
            'limits' => $this->limits(),
        ]);
    }

    /**
     * บันทึกไฟล์ + สกัดเนื้อหา (sync)
     */
    public function store(Request $request)
    {
        $request->validate([
            'document' => ['required', 'file', 'max:' . ($this->maxKb())],
        ], [
            'document.required' => 'กรุณาเลือกไฟล์',
            'document.max' => 'ไฟล์ใหญ่เกิน ' . (int) config('ai-chat.documents.max_file_size_mb', 20) . ' MB',
        ]);

        // ตรวจ extension เอง (Laravel 9 ไม่มี rule extensions: — ใช้ mimes เจอปัญหา docx=zip/fake)
        $allowed = $this->extensionsFlat();
        $ext = strtolower((string) ($request->file('document')->getClientOriginalExtension() ?: ''));
        if (!in_array($ext, $allowed, true)) {
            return redirect()->back()->withInput()->withErrors([
                'document' => 'ประเภทไฟล์ไม่รองรับ — รับได้: ' . implode(', ', $allowed),
            ]);
        }

        try {
            $doc = $this->documents->storeUpload($request->file('document'), $request->user());

            $message = $doc->status === Document::STATUS_READY
                ? "อัปโหลดสำเร็จ: {$doc->title} (พร้อมให้ AI ค้นแล้ว)"
                : "บันทึกไฟล์แล้ว: {$doc->title} แต่ยังอ่านเนื้อหาไม่ได้ ({$doc->fail_reason})";

            return redirect()->route('documents.index')->with('success', $message);
        } catch (\Throwable $e) {
            Log::warning('Document upload failed', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'อัปโหลดไม่สำเร็จ: ' . $e->getMessage());
        }
    }

    /**
     * ลบเอกสาร + ไฟล์
     */
    public function destroy(Request $request, int $id)
    {
        $deleted = $this->documents->deleteDocument($id, $request->user());
        $msg = $deleted ? 'ลบเอกสารแล้ว' : 'ไม่พบเอกสาร';

        return redirect()->route('documents.index')->with($deleted ? 'success' : 'error', $msg);
    }

    /**
     * ดาวน์โหลดไฟล์ต้นฉบับ (staff เท่านั้น)
     */
    public function download(int $id)
    {
        $doc = Document::findOrFail($id);
        $disk = (string) config('ai-chat.documents.storage_disk', 'local');

        if (!Storage::disk($disk)->exists($doc->storage_path)) {
            abort(404, 'ไม่พบไฟล์');
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $fs */
        $fs = Storage::disk($disk);

        return $fs->download($doc->storage_path, $doc->original_filename);
    }

    /**
     * ดูไฟล์ในเบราว์เซอร์ (preview) — staff เท่านั้น
     */
    public function preview(int $id)
    {
        $doc = Document::findOrFail($id);
        $disk = (string) config('ai-chat.documents.storage_disk', 'local');

        if (!Storage::disk($disk)->exists($doc->storage_path)) {
            abort(404, 'ไฟล์ไม่พบ');
        }

        $content = Storage::disk($disk)->get($doc->storage_path);

        return Response::make($content, 200, [
            'Content-Type' => $doc->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . addslashes($doc->original_filename) . '"',
            'X-Accel-Buffering' => 'no',
            'Cache-Control' => 'private, max-age=0, no-store',
        ]);
    }

    // ── ข้อมูล limit สำหรับหน้า UI ──────────────────────────────────────────

    protected function limits(): array
    {
        $ext = (array) config('ai-chat.documents.allowed_extensions', []);
        return [
            'extensions' => $ext,
            'mimes' => implode(',', array_merge(...array_values($ext))),
            'max_mb' => (int) config('ai-chat.documents.max_file_size_mb', 20),
        ];
    }

    protected function extensionsString(): string
    {
        return implode(',', $this->extensionsFlat());
    }

    protected function extensionsFlat(): array
    {
        $exts = (array) config('ai-chat.documents.allowed_extensions', []);
        return array_merge(...array_values($exts));
    }

    protected function maxKb(): int
    {
        return (int) config('ai-chat.documents.max_file_size_mb', 20) * 1024;
    }
}
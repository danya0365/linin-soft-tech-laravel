@extends('layouts.app')
@section('template_title')
    เอกสาร
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'เอกสาร']]" />

    <x-ui.card>
        <x-slot:header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">เอกสารที่อัปโหลด</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        อัปโหลดไฟล์ (รูป / PDF / เอกสาร) ให้ AI ตอบคำถามจากเนื้อหาได้ — นามสกุลที่รับ: {{ $limits['mimes'] ?? '...' }}
                        ขนาดไม่เกิน {{ $limits['max_mb'] }} MB
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <x-ui.button :href="route('documents.create')" variant="primary" icon="fa fa-upload">อัปโหลดเอกสาร</x-ui.button>
                </div>
            </div>
        </x-slot:header>

        @if (Session::has('success'))
            <x-ui.alert variant="success" dismissible="true">{{ Session::get('success') }}</x-ui.alert>
        @endif
        @if (Session::has('error'))
            <x-ui.alert variant="danger" dismissible="true">{{ Session::get('error') }}</x-ui.alert>
        @endif

        <div class="overflow-x-auto rounded-lg shadow">
            <table class="w-full text-sm text-left border-collapse bg-white dark:bg-gray-800">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800 border-b-2 border-gray-300 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-16 text-center">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">ชื่อไฟล์</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-20">ชนิด</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-20">สถานะ</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-52">อัปโหลดโดย</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-40 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent dark:hover:from-gray-700/50 dark:hover:to-transparent transition-all duration-200">
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm font-semibold">
                                    {{ $doc->id }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $doc->title }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $doc->original_filename }} · {{ number_format($doc->size_bytes) }} B</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                    {{ match($doc->kind) { 'image' => 'รูปภาพ', 'pdf' => 'PDF', 'office' => 'เอกสาร', default => $doc->kind } }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($doc->status === 'ready')
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                        <i class="fa fa-circle text-[8px]"></i> พร้อมใช้
                                    </span>
                                @elseif ($doc->status === 'failed')
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300"
                                          title="{{ $doc->fail_reason }}">ล้มเหลว</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">ประมวลผล</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-300">
                                {{ $doc->user->name ?? '—' }}<br>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ optional($doc->created_at)->format('d/m/y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex gap-1 items-center">
                                    @if ($doc->kind === 'image')
                                        <x-ui.button :href="route('documents.preview', $doc)" variant="ghost" icon="fa fa-eye" class="!px-2 !py-1">ดู</x-ui.button>
                                    @else
                                        <x-ui.button :href="route('documents.preview', $doc)" variant="ghost" icon="fa fa-eye" class="!px-2 !py-1">ดู</x-ui.button>
                                    @endif
                                    <x-ui.button :href="route('documents.download', $doc)" variant="ghost" icon="fa fa-download" class="!px-2 !py-1">ดาวน์โหลด</x-ui.button>
                                    <form method="POST" action="{{ route('documents.destroy', $doc) }}" class="inline" onsubmit="return confirm('ลบเอกสารนี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="danger" icon="fa fa-trash" class="!px-2 !py-1">ลบ</x-ui.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa fa-folder-open text-4xl text-gray-300 dark:text-gray-600"></i>
                                    <p>ยังไม่มีเอกสาร — กด "อัปโหลดเอกสาร" เพื่อเริ่ม</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    <div class="mt-6">{{ $documents->links() }}</div>
</div>
@endsection
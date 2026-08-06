@extends('layouts.app')
@section('template_title')
    อัปโหลดเอกสาร
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <x-crud.breadcrumb :items="[['label' => 'เอกสาร', 'route' => route('documents.index')], ['label' => 'อัปโหลด']]" />

    <x-ui.card>
        <x-slot:header>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">อัปโหลดเอกสารใหม่</h2>
        </x-slot:header>

        @if (Session::has('error'))
            <x-ui.alert variant="danger" dismissible="true">{{ Session::get('error') }}</x-ui.alert>
        @endif
        @if ($errors->any())
            <x-ui.alert variant="danger" dismissible="true">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        เลือกไฟล์ <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="document" accept="{{ $limits['extensions'] ? '.' . implode(',.', array_merge(...array_values($limits['extensions']))) : '' }}"
                           class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        รับไฟล์: <strong>{{ implode(', ', array_merge(...array_values($limits['extensions']))) }}</strong>
                        · ขนาดไม่เกิน <strong>{{ $limits['max_mb'] }} MB</strong>
                        · PDF สแกนไม่เกิน {{ config('ai-chat.documents.max_pdf_pages', 30) }} หน้า
                    </p>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 text-xs text-blue-800 dark:text-blue-300">
                    <i class="fa fa-info-circle mr-1"></i>
                    ระบบจะสกัดข้อความจากไฟล์ (OCR รูป / อ่าน PDF) ให้ AI นำไปตอบคำถามได้ —
                    ไฟล์ .doc/.docx/.xls/.xlsx จะถูกเก็บไว้แต่ยังอ่านเนื้อหาไม่ได้ในเฟสนี้
                </div>

                <div class="flex gap-2 pt-2">
                    <x-ui.button type="submit" variant="primary" icon="fa fa-upload">อัปโหลด</x-ui.button>
                    <x-ui.button :href="route('documents.index')" variant="ghost">ยกเลิก</x-ui.button>
                </div>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
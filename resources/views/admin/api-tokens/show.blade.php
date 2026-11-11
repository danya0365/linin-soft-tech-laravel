@extends('layouts.admin')

@section('content')
<x-admin.page
    title="Token สร้างสำเร็จ"
    subtitle="คัดลอก secret ตอนนี้ — ระบบจะไม่แสดงอีกครั้ง"
    icon="fa-key"
    icon-color="text-indigo-600"
    :breadcrumbs="[
        ['label' => 'API Tokens', 'route' => route('admin.api-tokens.index')],
        ['label' => 'Token ใหม่']
    ]"
>
    <div class="max-w-2xl space-y-4">
        <div class="rounded-xl border border-amber-300 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-700 px-4 py-3 text-sm text-amber-900 dark:text-amber-100">
            <strong>สำคัญ:</strong> แสดง secret ครั้งเดียวเท่านั้น กรุณาคัดลอกไปเก็บในที่ปลอดภัย
        </div>

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-6 space-y-4">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">ชื่อ</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $token->name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">สิทธิ์</p>
                <p class="text-sm text-gray-800 dark:text-gray-200">{{ $permissionLabel }}</p>
                <p class="text-xs text-indigo-600 dark:text-indigo-300 mt-1">
                    {{ implode(', ', (array) $token->abilities) }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Bearer Token</p>
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="text" readonly id="plain-token" value="{{ $plainTextToken }}"
                           class="flex-1 font-mono text-xs sm:text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-emerald-300 bg-gray-50">
                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('plain-token').value); this.textContent='Copied';"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 whitespace-nowrap">
                        Copy
                    </button>
                </div>
            </div>
            <div class="pt-2 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                <p>ตัวอย่างเรียกใช้:</p>
                <pre class="overflow-x-auto rounded-lg bg-gray-900 text-gray-100 p-3 text-xs">curl -H "Authorization: Bearer {{ $plainTextToken }}" \
  -H "Accept: application/json" \
  "{{ url('/api/v1/dashboard') }}"</pre>
                <p class="mt-2">
                    เอกสารเต็มสำหรับ AI / โปรเจคแยก:
                    <a class="text-indigo-600 dark:text-indigo-300 underline" href="{{ route('docs.external-api') }}" target="_blank" rel="noopener">Spec</a>
                    ·
                    <a class="text-indigo-600 dark:text-indigo-300 underline" href="{{ route('docs.external-api.json') }}" target="_blank" rel="noopener">JSON</a>
                    ·
                    <a class="text-indigo-600 dark:text-indigo-300 underline" href="{{ route('docs.external-api.swagger') }}" target="_blank" rel="noopener">Swagger Try it out</a>
                </p>
            </div>
        </div>

        <a href="{{ route('admin.api-tokens.index') }}"
           class="inline-flex px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200">
            กลับไปรายการ Token
        </a>
    </div>
</x-admin.page>
@endsection

@extends('layouts.admin')

@section('content')
<x-admin.page
    title="สร้าง API Token"
    subtitle="เลือกชื่อและระดับสิทธิ์ — secret จะแสดงครั้งเดียวหลังสร้าง"
    icon="fa-key"
    icon-color="text-indigo-600"
    :breadcrumbs="[
        ['label' => 'API Tokens', 'route' => route('admin.api-tokens.index')],
        ['label' => 'สร้าง']
    ]"
>
    <div class="max-w-xl rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.api-tokens.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ชื่อ Token</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="255"
                       placeholder="เช่น Mobile Dashboard"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <p class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ระดับสิทธิ์</p>
                <div class="space-y-3">
                    @foreach($permissionLevels as $key => $level)
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50
                            {{ old('permission_level', $defaultLevel) === $key ? 'ring-2 ring-indigo-500' : '' }}">
                            <input type="radio" name="permission_level" value="{{ $key }}"
                                   class="mt-1"
                                   {{ old('permission_level', $defaultLevel) === $key ? 'checked' : '' }}>
                            <span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $level['label'] }}</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $level['description'] }}</span>
                                <span class="block text-xs text-indigo-600 dark:text-indigo-300 mt-1">
                                    abilities: {{ implode(', ', $level['abilities']) }}
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('permission_level')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                    สร้าง Token
                </button>
                <a href="{{ route('admin.api-tokens.index') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200">
                    ยกเลิก
                </a>
            </div>
        </form>
    </div>
</x-admin.page>
@endsection

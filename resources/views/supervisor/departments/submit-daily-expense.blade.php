@extends('layouts.supervisor')

@section('content')
<x-supervisor.page 
    title="บันทึกค่าใช้จ่ายรายวัน" 
    subtitle="Record Daily Expense" 
    icon="fa-file-invoice-dollar"
    :breadcrumbs="[
        ['label' => __('Department'), 'route' => route('supervisor.department')],
        ['label' => 'บันทึกค่าใช้จ่ายรายวัน']
    ]"
>
    <div class="max-w-3xl mx-auto">
        @if ($message = Session::get('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 flex items-center">
            <i class="fa fa-check-circle mr-2"></i>
            {{ $message }}
        </div>
        @endif

        <x-supervisor.card title="เพิ่มบันทึกค่าใช้จ่ายรายวัน">
            <form action="{{ request()->url() }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                {{-- Department Select --}}
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        แผนก
                    </label>
                    <select id="department_id" name="department_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">ไม่เลือก</option>
                        @foreach ( $departments as $department )
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date --}}
                <div>
                    <label for="daily_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        วันที่
                    </label>
                    <input type="date" id="daily_date" name="daily_date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('daily_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cost --}}
                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        จำนวนเงิน
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-gray-500 sm:text-sm">฿</span>
                        </div>
                        <input type="number" id="cost" name="cost" class="pl-7 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00" />
                    </div>
                    @error('cost')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Note --}}
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        บันทึกข้อความ - Note
                    </label>
                    <textarea id="message" name="message" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Image Upload --}}
                <div>
                    <label for="image_upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        อัพโหลดรูป - Attach Photo
                    </label>
                    <input type="file" id="image_upload" name="image_upload" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900 dark:file:text-indigo-300" />
                    @error('image_upload')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 flex items-center">
                        <i class="fa fa-save mr-2"></i> Submit
                    </button>
                    <button type="reset" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 font-medium rounded-lg shadow-sm transition-colors duration-200">
                        Reset
                    </button>
                </div>
            </form>
        </x-supervisor.card>
    </div>
</x-supervisor.page>
@endsection
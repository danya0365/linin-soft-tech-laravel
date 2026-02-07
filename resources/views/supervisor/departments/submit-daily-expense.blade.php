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
                @php
                    $departmentOptions = [];
                    foreach ($departments as $department) {
                        $departmentOptions[$department->id] = $department->name;
                    }
                @endphp
                <x-crud.form-group 
                    name="department_id" 
                    label="แผนก" 
                    type="select" 
                    :options="$departmentOptions" 
                    placeholder="ไม่เลือก"
                />

                {{-- Date --}}
                <x-crud.form-group 
                    name="daily_date" 
                    label="วันที่" 
                    type="date" 
                />

                {{-- Cost --}}
                <x-crud.form-group 
                    name="cost" 
                    label="จำนวนเงิน" 
                    type="number" 
                    placeholder="0.00" 
                    prefix="฿"
                />

                {{-- Note --}}
                <x-crud.form-group 
                    name="message" 
                    label="บันทึกข้อความ - Note" 
                    type="textarea" 
                    rows="3" 
                />

                {{-- Image Upload --}}
                <x-crud.form-group 
                    name="image_upload" 
                    label="อัพโหลดรูป - Attach Photo" 
                    type="file" 
                />

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
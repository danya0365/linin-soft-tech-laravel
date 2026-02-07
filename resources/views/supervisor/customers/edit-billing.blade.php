@extends('layouts.supervisor')

@section('content')
<x-supervisor.page 
    title="{{ __('แก้ไขบิลรายรับ') }}"
    subtitle="Edit Billing" 
    icon="fa-file-invoice-dollar"
    :breadcrumbs="[
        ['label' => 'Supervisor', 'route' => route('supervisor')],
        ['label' => __('Customer'), 'route' => route('supervisor.customer')],
        ['label' => __('Billing Logs'), 'route' => route('supervisor.customer.billing-logs')],
        ['label' => __('แก้ไขบิลรายรับ')]
    ]"
>
    <div class="max-w-4xl mx-auto">
        <x-supervisor.card title="แก้ไขบิลรายรับ - ลูกค้า: {{ $operation->customer->name ?? '-' }}">
            <form method="POST" action="{{ route('supervisor.customer.billing-logs.edit', $operation->id) }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf
                
                {{-- Customer (Disabled) --}}
                <div class="md:col-span-2">
                    <x-crud.form-group 
                        name="customer_name" 
                        label="ลูกค้า" 
                        type="text" 
                        :value="$operation->customer->name ?? '-'" 
                        disabled 
                    />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">ไม่สามารถเปลี่ยนลูกค้าได้</p>
                </div>

                {{-- Billing Weight --}}
                <div class="md:col-span-2">
                    <x-crud.form-group 
                        name="total_billing_weight" 
                        label="น้ำหนักลูกค้า (kg.)" 
                        type="number" 
                        step=".01" 
                        :value="$operation->total_billing_weight" 
                        oninput="calculateEditWeightPercent()" 
                    />
                </div>

                {{-- Edit Weight --}}
                <div class="md:col-span-1">
                    <x-crud.form-group 
                        name="total_edit_weight" 
                        label="น้ำหนักผ้าแก้ไข (kg.)" 
                        type="number" 
                        step=".01" 
                        :value="$operation->total_edit_weight" 
                        oninput="calculateEditWeightPercent()" 
                    />
                </div>

                {{-- Edit Percent --}}
                <div class="md:col-span-1">
                    <x-crud.form-group 
                        name="edit_weight_percent" 
                        label="% ผ้าแก้ไข" 
                        type="text" 
                        readonly 
                        suffix="%" 
                        help="คำนวณจาก (น้ำหนักผ้าแก้ไข / น้ำหนักลูกค้า) × 100" 
                        class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                    />
                </div>

                {{-- Payment (Disabled) --}}
                <div class="md:col-span-2">
                    <x-crud.form-group 
                        name="total_billing_payment" 
                        label="ยอดเก็บเงิน (Thai Baht)" 
                        type="number" 
                        step=".01" 
                        :value="$operation->total_billing_payment" 
                        disabled 
                    />
                    <p class="mt-1 text-xs text-amber-600 dark:text-amber-400 flex items-center">
                        <i class="fa fa-lock mr-1"></i> ไม่สามารถแก้ไขได้ เพราะผูกกับรายได้ในระบบ
                    </p>
                </div>

                {{-- Payment Date (Disabled) --}}
                <div class="md:col-span-2">
                    <x-crud.form-group 
                        name="billing_payment_date" 
                        label="วันที่เก็บเงิน" 
                        type="date" 
                        :value="$operation->billing_payment_date" 
                        disabled 
                    />
                    <p class="mt-1 text-xs text-amber-600 dark:text-amber-400 flex items-center">
                        <i class="fa fa-lock mr-1"></i> ไม่สามารถแก้ไขได้ เพราะผูกกับรายงานสรุปรายวัน
                    </p>
                </div>

                {{-- Buttons --}}
                <div class="md:col-span-2 flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700 mt-2">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 flex items-center">
                        <i class="fa fa-save mr-2"></i> บันทึกการแก้ไข
                    </button>
                    <a href="{{ route('supervisor.customer.billing-logs') }}" class="px-6 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 font-medium rounded-lg shadow-sm transition-colors duration-200">
                        ยกเลิก
                    </a>
                </div>
            </form>
        </x-supervisor.card>
    </div>
</x-supervisor.page>

@push('scripts')
<script>
function calculateEditWeightPercent() {
    var billingWeight = parseFloat(document.getElementById('total_billing_weight').value) || 0;
    var editWeight = parseFloat(document.getElementById('total_edit_weight').value) || 0;
    var editPercent = 0;
    
    if (billingWeight > 0 && editWeight > 0) {
        editPercent = (editWeight / billingWeight * 100).toFixed(2);
    }
    
    document.getElementById('edit_weight_percent').value = editPercent;
}

// คำนวณ % เมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', function() {
    calculateEditWeightPercent();
});
</script>
@endpush
@endsection

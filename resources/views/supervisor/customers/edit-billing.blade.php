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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        ลูกค้า
                    </label>
                    <input type="text" value="{{ $operation->customer->name ?? '-' }}" disabled class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 shadow-sm cursor-not-allowed" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">ไม่สามารถเปลี่ยนลูกค้าได้</p>
                </div>

                {{-- Billing Weight --}}
                <div class="md:col-span-2">
                    <label for="total_billing_weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        น้ำหนักลูกค้า (kg.)
                    </label>
                    <input type="number" step=".01" name="total_billing_weight" id="total_billing_weight" value="{{ $operation->total_billing_weight }}" oninput="calculateEditWeightPercent()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('total_billing_weight')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Edit Weight --}}
                <div class="md:col-span-1">
                    <label for="total_edit_weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        น้ำหนักผ้าแก้ไข (kg.)
                    </label>
                    <input type="number" step=".01" name="total_edit_weight" id="total_edit_weight" value="{{ $operation->total_edit_weight }}" oninput="calculateEditWeightPercent()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('total_edit_weight')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Edit Percent --}}
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        % ผ้าแก้ไข
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <input type="text" id="edit_weight_percent" readonly class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 shadow-sm focus:ring-0 focus:border-gray-300 pr-10" />
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <span class="text-gray-500 sm:text-sm">%</span>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">คำนวณจาก (น้ำหนักผ้าแก้ไข / น้ำหนักลูกค้า) × 100</p>
                </div>

                {{-- Payment (Disabled) --}}
                <div class="md:col-span-2">
                    <label for="total_billing_payment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        ยอดเก็บเงิน (Thai Baht)
                    </label>
                    <input type="number" step=".01" id="total_billing_payment" value="{{ $operation->total_billing_payment }}" disabled class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 shadow-sm cursor-not-allowed" />
                    <p class="mt-1 text-xs text-amber-600 dark:text-amber-400 flex items-center">
                        <i class="fa fa-lock mr-1"></i> ไม่สามารถแก้ไขได้ เพราะผูกกับรายได้ในระบบ
                    </p>
                </div>

                {{-- Payment Date (Disabled) --}}
                <div class="md:col-span-2">
                    <label for="billing_payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        วันที่เก็บเงิน
                    </label>
                    <input type="date" id="billing_payment_date" value="{{ $operation->billing_payment_date }}" disabled class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 shadow-sm cursor-not-allowed" />
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

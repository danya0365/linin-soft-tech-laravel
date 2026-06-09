@extends('layouts.supervisor')

@section('content')
<x-supervisor.page 
    title="{{ __('เพิ่มบิลรายรับ') }}"
    subtitle="New Income Billing" 
    icon="fa-file-invoice-dollar"
    :breadcrumbs="[
        ['label' => __('Customer'), 'route' => route('supervisor.customer')],
        ['label' => __('เพิ่มบิลรายรับ')]
    ]"
>
    <div class="max-w-4xl mx-auto">
        <x-supervisor.card title="น้ำหนักลูกค้าและยอดเก็บเงิน">
            <form method="POST" action="{{ route('supervisor.customer.new-billing') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf
                
                {{-- Customer Select (Keep manual for optgroups support) --}}
                <div class="md:col-span-2">
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                        ลูกค้า
                    </label>
                    <select id="customer_id" name="customer_id" required class="w-full px-4 py-2.5 border rounded-lg transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed border-gray-300 dark:border-gray-600 shadow-sm">
                        <option value="">เลือกลูกค้า</option>
                        @foreach ( $customerGroups as $customerGroup )
                        <optgroup label="{{ $customerGroup['name'] }}">
                            @foreach ( $customerGroup['customers'] as $customer )
                            <option value="{{ $customer['id'] }}">{{ $customer['name'] }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>

                {{-- Billing Weight --}}
                <div class="md:col-span-2">
                    <x-crud.form-group 
                        name="total_billing_weight" 
                        label="น้ำหนักลูกค้า (kg.)" 
                        type="number" 
                        step=".01" 
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

                {{-- Payment --}}
                <div class="md:col-span-2">
                    <x-crud.form-group 
                        name="total_billing_payment" 
                        label="ยอดเก็บเงิน (Thai Baht)" 
                        type="number" 
                        step=".01" 
                        id="cost"
                    />
                </div>

                {{-- Payment Date --}}
                <div class="md:col-span-2">
                    <x-crud.form-group 
                        name="billing_payment_date" 
                        label="วันที่เก็บเงิน" 
                        type="date" 
                    />
                </div>

                {{-- Buttons --}}
                <div class="md:col-span-2 flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700 mt-2">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 flex items-center">
                        <i class="fa fa-save mr-2"></i> Submit
                    </button>
                    <button type="reset" class="px-6 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 font-medium rounded-lg shadow-sm transition-colors duration-200">
                        Reset
                    </button>
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
</script>
@endpush
@endsection
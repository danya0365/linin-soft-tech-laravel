@extends('layouts.worker')

@section('content')
<x-worker.page
    :breadcrumbs="[
        ['label' => __('Stocks'), 'route' => route('worker.stock')],
        ['label' => $inventory->inventoryGroup->name, 'route' => route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventory->inventoryGroup->id])]
    ]"
    title="{{ __('Decrease Stock') }}"
    subtitle="{{ $inventory->name }}"
    icon="fa-minus-circle"
>
    <x-worker.card :title="__('ลดสต๊อก / เบิกของ - Decrease Stock')">
        <form method="POST" action="{{ request()->url() }}" enctype="multipart/form-data">
            @csrf

            {{-- Current Info (Read-only) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">ชื่อ - Name</label>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $inventory->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">หน่วย - Unit</label>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $inventory->unit }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">สต๊อกทั้งหมด - Total</label>
                    <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ $inventory->total_quantity }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">สต๊อกคงเหลือ - Remain</label>
                    <p id="remain_quantity_display" class="text-lg font-semibold text-green-600 dark:text-green-400">{{ $inventory->remain_quantity }}</p>
                </div>
            </div>

            {{-- Decrease Amount --}}
            <x-crud.form-group 
                name="decrease_quantity" 
                label="จำนวนที่ต้องการลด / เบิก - Decrease Count" 
                type="number" 
                :value="old('decrease_quantity', 0)" 
                placeholder="Enter quantity to decrease"
            />

            <x-crud.form-group 
                name="cost" 
                label="ค่าใช้จ่าย - Cost (Thai Baht)" 
                type="number" 
                :value="old('cost', 0)" 
                placeholder="Enter cost"
            />

            <x-crud.form-group 
                name="created_at" 
                label="วันที่เบิกของ" 
                type="date" 
                :value="old('created_at')" 
            />

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-ui.button type="submit" variant="danger" icon="fa fa-minus">
                    {{ __('Decrease Stock') }}
                </x-ui.button>
                <x-ui.button :href="route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventory->inventoryGroup->id])" variant="secondary" icon="fa fa-times">
                    {{ __('Cancel') }}
                </x-ui.button>
            </div>
        </form>
    </x-worker.card>
</x-worker.page>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var remainQuantity = {{ $inventory->remain_quantity }};
    var input = document.querySelector('[name="decrease_quantity"]');
    
    if (input) {
        input.addEventListener('input', function() {
            var decrease = parseInt(this.value) || 0;
            // Prevent decreasing more than available
            if (decrease > remainQuantity) {
                decrease = remainQuantity;
                this.value = decrease;
            }
            document.getElementById('remain_quantity_display').textContent = remainQuantity - decrease;
        });
    }
});
</script>
@endpush
@endsection
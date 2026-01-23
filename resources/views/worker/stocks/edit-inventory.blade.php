@extends('layouts.worker')

@section('content')
<x-worker.page
    :breadcrumbs="[
        ['label' => __('Stocks'), 'route' => route('worker.stock')],
        ['label' => $inventory->inventoryGroup->name, 'route' => route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventory->inventoryGroup->id])]
    ]"
    title="{{ __('Edit') }} {{ $inventory->name }}"
>
    <x-worker.card :title="__('Edit Inventory')">
        <form method="POST" action="{{ request()->url() }}" enctype="multipart/form-data">
            @csrf

            <x-crud.form-group 
                name="name" 
                label="ชื่อ - Name" 
                type="text" 
                :value="$inventory->name ?? old('name')" 
                placeholder="Enter Name" 
            />

            <x-crud.form-group 
                name="unit" 
                label="หน่วย - Unit" 
                type="text" 
                :value="$inventory->unit ?? old('unit')" 
                placeholder="e.g., pcs, kg, liters" 
            />

            <x-crud.form-group 
                name="total_quantity" 
                label="จำนวนสต๊อกทั้งหมด - Total Quantity" 
                type="text" 
                :value="$inventory->total_quantity ?? old('total_quantity')" 
                placeholder="Total Quantity"
                readonly="true"
            />

            <x-crud.form-group 
                name="remain_quantity" 
                label="จำนวนสต๊อกคงเหลือ - Remain Quantity" 
                type="text" 
                :value="$inventory->remain_quantity ?? old('remain_quantity')" 
                placeholder="Remain Quantity"
                readonly="true"
            />

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-ui.button type="submit" variant="primary" icon="fa fa-save">
                    {{ __('Save') }}
                </x-ui.button>
                <x-ui.button :href="route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventory->inventoryGroup->id])" variant="secondary" icon="fa fa-times">
                    {{ __('Cancel') }}
                </x-ui.button>
            </div>
        </form>
    </x-worker.card>
</x-worker.page>
@endsection
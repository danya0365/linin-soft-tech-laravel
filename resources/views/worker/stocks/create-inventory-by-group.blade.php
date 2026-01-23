@extends('layouts.worker')

@section('content')
<x-worker.page
    :breadcrumbs="[
        ['label' => __('Stocks'), 'route' => route('worker.stock')],
        ['label' => $inventoryGroup->name, 'route' => route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventoryGroup->id])],
        ['label' => __('Create New')]
    ]"
>
    <x-worker.card :title="__('Create New Inventory') . ' - ' . $inventoryGroup->name">
        <form method="POST" action="{{ request()->url() }}" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="inventory_group_id" value="{{ $inventoryGroup->id }}" />

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

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-ui.button type="submit" variant="primary" icon="fa fa-save">
                    {{ __('Save') }}
                </x-ui.button>
                <x-ui.button :href="route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventoryGroup->id])" variant="secondary" icon="fa fa-times">
                    {{ __('Cancel') }}
                </x-ui.button>
            </div>
        </form>
    </x-worker.card>
</x-worker.page>
@endsection
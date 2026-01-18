@extends('layouts.app')
@section('template_title')
    Show Inventory Stock Log
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Inventory Stock Log', 'route' => 'inventory-stock-logs.index'],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Inventory Stock Log Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button :href="route('inventory-stock-logs.edit', $inventoryStockLog->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('inventory-stock-logs.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Employee Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $inventoryStockLog->employee_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Inventory Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $inventoryStockLog->inventory_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Type</p><p class="font-semibold text-gray-900 dark:text-white">{{ $inventoryStockLog->type ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Quantity</p><p class="font-semibold text-gray-900 dark:text-white">{{ $inventoryStockLog->quantity ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Cost</p><p class="font-semibold text-gray-900 dark:text-white">{{ $inventoryStockLog->cost ?? '-' }}</p></div>
        </div>
    </x-ui.card>
</div>
@endsection
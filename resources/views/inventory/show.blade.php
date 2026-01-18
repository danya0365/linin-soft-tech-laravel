@extends('layouts.app')
@section('template_title')
    Show Inventory
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Inventory', 'route' => 'inventories.index'],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Inventory Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button :href="route('inventories.edit', $inventory->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('inventories.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Inventory Group Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $inventory->inventory_group_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Name</p><p class="font-semibold text-gray-900 dark:text-white">{{ $inventory->name ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Unit</p><p class="font-semibold text-gray-900 dark:text-white">{{ $inventory->unit ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Quantity</p><p class="font-semibold text-gray-900 dark:text-white">{{ $inventory->total_quantity ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Remain Quantity</p><p class="font-semibold text-gray-900 dark:text-white">{{ $inventory->remain_quantity ?? '-' }}</p></div>
        </div>
    </x-ui.card>
</div>
@endsection
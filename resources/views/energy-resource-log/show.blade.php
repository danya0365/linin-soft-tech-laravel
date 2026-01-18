@extends('layouts.app')
@section('template_title')
    Show Energy Resource Log
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Energy Resource Log', 'route' => 'energy-resource-log.index'],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Energy Resource Log Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button :href="route('energy-resource-log.edit', $energyResourceLog->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('energy-resource-log.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Energy Resource Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $energyResourceLog->energy_resource_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Employee Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $energyResourceLog->employee_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Value</p><p class="font-semibold text-gray-900 dark:text-white">{{ $energyResourceLog->value ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Unit</p><p class="font-semibold text-gray-900 dark:text-white">{{ $energyResourceLog->unit ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Lot Number</p><p class="font-semibold text-gray-900 dark:text-white">{{ $energyResourceLog->lot_number ?? '-' }}</p></div>
        </div>
    </x-ui.card>
</div>
@endsection
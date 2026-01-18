@extends('layouts.app')
@section('template_title')
    Show Operation
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Operation', 'route' => 'operation.index'],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Operation Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button :href="route('operation.edit', $operation->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('operation.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Operation Type</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->operation_type ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Employee Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->employee_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Customer Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->customer_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Wash Employee Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->wash_employee_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Dry Employee Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->dry_employee_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Iron Employee Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->iron_employee_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Packing Employee Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->packing_employee_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Collect Employee Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->collect_employee_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Job Case</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->job_case ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Washing Machine Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->washing_machine_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Dryer Machine Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->dryer_machine_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Wet Weight</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->total_wet_weight ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Dry Weight</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->total_dry_weight ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Iron Piece</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->total_iron_piece ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Packing Piece</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->total_packing_piece ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Colors</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->colors ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Search Tags</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->search_tags ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Status</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operation->status ?? '-' }}</p></div>
        </div>
    </x-ui.card>
</div>
@endsection
@extends('layouts.app')
@section('template_title')
    Show Operation Log
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Operation Log', 'route' => 'operation-logs.index'],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Operation Log Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button :href="route('operation-logs.edit', $operationLog->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('operation-logs.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Operation Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operationLog->operation_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Employee Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operationLog->employee_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Action Name</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operationLog->action_name ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Old Values</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operationLog->old_values ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">New Values</p><p class="font-semibold text-gray-900 dark:text-white">{{ $operationLog->new_values ?? '-' }}</p></div>
        </div>
    </x-ui.card>
</div>
@endsection
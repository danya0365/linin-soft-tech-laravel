@extends('layouts.app')
@section('template_title')
    {{ $employee->name ?? 'Show Employee' }}
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Employees', 'route' => 'employees.index'],['label' => $employee->name]]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Employee Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button :href="route('employees.edit', $employee->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('employees.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Code</p><p class="font-semibold text-gray-900 dark:text-white">{{ $employee->code }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Name</p><p class="font-semibold text-gray-900 dark:text-white">{{ $employee->name }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Department</p><p class="font-semibold text-gray-900 dark:text-white">{{ $employee->department ? $employee->department->name : '-' }}</p></div>
            <div class="md:col-span-2"><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Photo</p>@if($employee->photo)<img src="{{ asset($employee->photo) }}" alt="{{ $employee->name }}" class="h-32 w-32 rounded-lg object-cover ring-2 ring-gray-200 dark:ring-gray-600">@else<p class="text-gray-400 dark:text-gray-500">No photo</p>@endif</div>
        </div>
    </x-ui.card>
</div>
@endsection

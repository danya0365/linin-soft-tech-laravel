@extends('layouts.app')
@section('template_title')
    Show Employee
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => route('admin')],['label' => 'Employee', 'route' => route('employees.index')],['label' => 'Details']]" />
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
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Code</p><p class="font-semibold text-gray-900 dark:text-white">{{ $employee->code ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Name</p><p class="font-semibold text-gray-900 dark:text-white">{{ $employee->name ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Photo</p><p class="font-semibold text-gray-900 dark:text-white">{{ $employee->photo ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Department Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $employee->department_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Image Upload</p><p class="font-semibold text-gray-900 dark:text-white">{{ $employee->image_upload ?? '-' }}</p></div>
        </div>
    </x-ui.card>
</div>
@endsection
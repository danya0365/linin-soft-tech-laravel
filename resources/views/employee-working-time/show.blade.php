@extends('layouts.app')
@section('template_title')
    Show Employee Working Time
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Employee Working Time', 'route' => 'employee-working-time.index'],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Employee Working Time Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button :href="route('employee-working-time.edit', $employeeWorkingTime->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('employee-working-time.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Employee Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $employeeWorkingTime->employee_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Working Date</p><p class="font-semibold text-gray-900 dark:text-white">{{ $employeeWorkingTime->working_date ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Time Duration</p><p class="font-semibold text-gray-900 dark:text-white">{{ $employeeWorkingTime->time_duration ?? '-' }}</p></div>
        </div>
    </x-ui.card>
</div>
@endsection
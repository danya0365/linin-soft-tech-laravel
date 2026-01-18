@extends('layouts.app')
@section('template_title')
    Employee Working Time
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Employee Working Time']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Employee Working Time" :createRoute="route('employee-working-time.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Employee Id', 'Working Date', 'Time Duration', 'Actions']"
            :data="$employeeWorkingTimes"
            :columns="['employee_id', 'working_date', 'time_duration']"
            resource="employee-working-time"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $employeeWorkingTimes->links() }}</div>
</div>
@endsection
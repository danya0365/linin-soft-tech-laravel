@extends('layouts.app')
@section('template_title')
    Employee Operation Log
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Employee Operation Log']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Employee Operation Log" :createRoute="route('employee-operation-logs.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Actions']"
            :data="$employeeOperationLogs"
            :columns="['']"
            resource="employee-operation-logs"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $employeeOperationLogs->links() }}</div>
</div>
@endsection
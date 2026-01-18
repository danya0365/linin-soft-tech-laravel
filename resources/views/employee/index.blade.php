@extends('layouts.app')
@section('template_title')
    Employee
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'พนักงาน (Employees)']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Employee" :createRoute="route('employees.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Code', 'Name', 'Photo', 'Department', 'Actions']"
            :data="$employees"
            :columns="['code', 'name', 'photo', 'department_name']"
            resource="employees"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $employees->links() }}</div>
</div>
@endsection

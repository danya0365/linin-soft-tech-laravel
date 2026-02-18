@extends('layouts.app')
@section('template_title')
    Update Employee
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => route('admin')],['label' => 'Employee', 'route' => route('employees.index')],['label' => 'Edit']]" />
    @includeif('partials.errors')
    <x-ui.card>
        <x-slot:header><h2 class="text-xl font-semibold text-gray-900 dark:text-white">Update Employee</h2></x-slot:header>
        <form method="POST" action="{{ route('employees.update', $employee->id) }}" role="form" enctype="multipart/form-data">
            {{ method_field('PATCH') }}
            @csrf
            @include('employee.form')
        </form>
    </x-ui.card>
</div>
@endsection
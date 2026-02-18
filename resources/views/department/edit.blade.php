@extends('layouts.app')
@section('template_title')
    Update Department
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => route('admin')],['label' => 'Department', 'route' => route('departments.index')],['label' => 'Edit']]" />
    @includeif('partials.errors')
    <x-ui.card>
        <x-slot:header><h2 class="text-xl font-semibold text-gray-900 dark:text-white">Update Department</h2></x-slot:header>
        <form method="POST" action="{{ route('departments.update', $department->id) }}" role="form" enctype="multipart/form-data">
            {{ method_field('PATCH') }}
            @csrf
            @include('department.form')
        </form>
    </x-ui.card>
</div>
@endsection
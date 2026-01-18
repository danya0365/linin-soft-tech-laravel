@extends('layouts.app')
@section('template_title')
    Create Employee Working Time
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Employee Working Time', 'route' => 'employee-working-time.index'],['label' => 'Create']]" />
    @includeif('partials.errors')
    <x-ui.card>
        <x-slot:header><h2 class="text-xl font-semibold text-gray-900 dark:text-white">Create Employee Working Time</h2></x-slot:header>
        <form method="POST" action="{{ route('employee-working-time.store') }}" role="form" enctype="multipart/form-data">
            @csrf
            @include('employee-working-time.form')
        </form>
    </x-ui.card>
</div>
@endsection
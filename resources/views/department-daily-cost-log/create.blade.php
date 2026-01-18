@extends('layouts.app')
@section('template_title')
    Create Department Daily Cost Log
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Department Daily Cost Log', 'route' => 'department-daily-cost-logs.index'],['label' => 'Create']]" />
    @includeif('partials.errors')
    <x-ui.card>
        <x-slot:header><h2 class="text-xl font-semibold text-gray-900 dark:text-white">Create Department Daily Cost Log</h2></x-slot:header>
        <form method="POST" action="{{ route('department-daily-cost-logs.store') }}" role="form" enctype="multipart/form-data">
            @csrf
            @include('department-daily-cost-log.form')
        </form>
    </x-ui.card>
</div>
@endsection
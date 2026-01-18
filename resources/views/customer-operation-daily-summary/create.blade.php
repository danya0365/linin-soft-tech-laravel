@extends('layouts.app')
@section('template_title')
    Create Customer Operation Daily Summary
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Customer Operation Daily Summary', 'route' => 'customer-operation-daily-summaries.index'],['label' => 'Create']]" />
    @includeif('partials.errors')
    <x-ui.card>
        <x-slot:header><h2 class="text-xl font-semibold text-gray-900 dark:text-white">Create Customer Operation Daily Summary</h2></x-slot:header>
        <form method="POST" action="{{ route('customer-operation-daily-summaries.store') }}" role="form" enctype="multipart/form-data">
            @csrf
            @include('customer-operation-daily-summary.form')
        </form>
    </x-ui.card>
</div>
@endsection
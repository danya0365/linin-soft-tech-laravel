@extends('layouts.app')
@section('template_title')
    Create Truck
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => route('admin')],['label' => 'Truck', 'route' => route('trucks.index')],['label' => 'Create']]" />
    @includeif('partials.errors')
    <x-ui.card>
        <x-slot:header><h2 class="text-xl font-semibold text-gray-900 dark:text-white">Create Truck</h2></x-slot:header>
        <form method="POST" action="{{ route('trucks.store') }}" role="form" enctype="multipart/form-data">
            @csrf
            @include('truck.form')
        </form>
    </x-ui.card>
</div>
@endsection
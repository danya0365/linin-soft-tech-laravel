@extends('layouts.app')
@section('template_title')
    Update Energy Resource Log
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Energy Resource Log', 'route' => 'energy-resource-log.index'],['label' => 'Edit']]" />
    @includeif('partials.errors')
    <x-ui.card>
        <x-slot:header><h2 class="text-xl font-semibold text-gray-900 dark:text-white">Update Energy Resource Log</h2></x-slot:header>
        <form method="POST" action="{{ route('energy-resource-log.update', $energyResourceLog->id) }}" role="form" enctype="multipart/form-data">
            {{ method_field('PATCH') }}
            @csrf
            @include('energy-resource-log.form')
        </form>
    </x-ui.card>
</div>
@endsection
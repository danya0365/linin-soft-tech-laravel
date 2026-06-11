@extends('layouts.app')
@section('template_title')
    Update Dryer Machine
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => route('admin')],['label' => 'Dryer Machine', 'route' => route('dryer-machines.index')],['label' => 'Edit']]" />
    @includeif('partials.errors')
    <x-ui.card>
        <x-slot:header><h2 class="text-xl font-semibold text-gray-900 dark:text-white">Update Dryer Machine</h2></x-slot:header>
        <form method="POST" action="{{ route('dryer-machines.update', $dryerMachine->id) }}" role="form" enctype="multipart/form-data">
            {{ method_field('PATCH') }}
            @csrf
            @include('dryer-machine.form')
        </form>
    </x-ui.card>
</div>
@endsection
@extends('layouts.app')
@section('template_title')
    Create User
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'User', 'route' => 'user.index'],['label' => 'Create']]" />
    @includeif('partials.errors')
    <x-ui.card>
        <x-slot:header><h2 class="text-xl font-semibold text-gray-900 dark:text-white">Create User</h2></x-slot:header>
        <form method="POST" action="{{ route('user.store') }}" role="form" enctype="multipart/form-data">
            @csrf
            @include('user.form')
        </form>
    </x-ui.card>
</div>
@endsection
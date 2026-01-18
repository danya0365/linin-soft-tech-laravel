@extends('layouts.app')
@section('template_title')
    Update Note
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Note', 'route' => 'note.index'],['label' => 'Edit']]" />
    @includeif('partials.errors')
    <x-ui.card>
        <x-slot:header><h2 class="text-xl font-semibold text-gray-900 dark:text-white">Update Note</h2></x-slot:header>
        <form method="POST" action="{{ route('note.update', $note->id) }}" role="form" enctype="multipart/form-data">
            {{ method_field('PATCH') }}
            @csrf
            @include('note.form')
        </form>
    </x-ui.card>
</div>
@endsection
@extends('layouts.app')

@section('template_title')
    Update Washing Machine
@endsection

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    
    {{-- Breadcrumb --}}
    <x-crud.breadcrumb :items="[
        ['label' => 'Admin', 'route' => 'admin'],
        ['label' => 'Washing Machines', 'route' => 'washing-machines.index'],
        ['label' => 'Edit']
    ]" />
    
    {{-- Validation Errors --}}
    @includeif('partials.errors')
    
    {{-- Edit Form Card --}}
    <x-ui.card>
        <x-slot:header>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Update Washing Machine</h2>
        </x-slot:header>
        
        <form method="POST" action="{{ route('washing-machines.update', $washingMachine->id) }}" role="form" enctype="multipart/form-data">
            {{ method_field('PATCH') }}
            @csrf
            
            @include('washing-machine.form')
            
        </form>
    </x-ui.card>
</div>
@endsection

@extends('layouts.app')

@section('template_title')
    Washing Machine
@endsection

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    
    {{-- Breadcrumb --}}
    <x-crud.breadcrumb :items="[
        ['label' => 'Admin', 'route' => 'admin'],
        ['label' => 'Washing Machines']
    ]" />
    
    {{-- Main Card --}}
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header 
                title="Washing Machines"
                :createRoute="route('washing-machines.create')"
            />
        </x-slot:header>
        
        {{-- Success Alert --}}
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">
                {{ $message }}
            </x-ui.alert>
        @endif
        
        {{-- Data Table --}}
        <x-crud.data-table 
            :headers="['No', 'Name', 'Photo', 'Maximum Weight', 'Actions']"
            :data="$washingMachines"
            :columns="['name', 'photo', 'maximum_weight']"
            resource="washing-machines"
            :startIndex="$i"
        />
    </x-ui.card>
    
    {{-- Pagination --}}
    <div class="mt-6">
        {{ $washingMachines->links() }}
    </div>
</div>
@endsection

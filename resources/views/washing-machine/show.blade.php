@extends('layouts.app')

@section('template_title')
    {{ $washingMachine->name ?? 'Show Washing Machine' }}
@endsection

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    
    {{-- Breadcrumb --}}
    <x-crud.breadcrumb :items="[
        ['label' => 'Admin', 'route' => 'admin'],
        ['label' => 'Washing Machines', 'route' => 'washing-machines.index'],
        ['label' => $washingMachine->name]
    ]" />
    
    {{-- Success Alert --}}
    @if (session('success'))
        <x-ui.alert variant="success" dismissible="true">
            {{ session('success') }}
        </x-ui.alert>
    @endif
    
    <div class="grid gap-6">
        {{-- Detail Card --}}
        <x-ui.card>
            <x-slot:header>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Washing Machine Details</h2>
                    <div class="flex items-center gap-2">
                        <x-ui.button 
                            :href="route('washing-machines.create-note', ['id' => $washingMachine->id])"
                            variant="primary"
                            size="sm"
                            icon="fa fa-plus"
                        >
                            Add Note
                        </x-ui.button>
                        <x-ui.button 
                            :href="route('washing-machines.edit', $washingMachine->id)"
                            variant="success"
                            size="sm"
                            icon="fa fa-edit"
                        >
                            Edit
                        </x-ui.button>
                        <x-ui.button 
                            :href="route('washing-machines.index')"
                            variant="secondary"
                            size="sm"
                            icon="fa fa-arrow-left"
                        >
                            Back
                        </x-ui.button>
                    </div>
                </div>
            </x-slot:header>
            
            <x-crud.detail-card 
                :model="$washingMachine" 
                :fields="[
                    'name' => 'Name',
                    'photo' => 'Photo',
                    'maximum_weight' => 'Maximum Weight (kg)'
                ]"
            />
        </x-ui.card>
        
        {{-- Notes Section --}}
        <x-crud.notes-section 
            :notes="$notes"
            :tags="$machineTags"
            :selectedTag="$selectedTag"
            :model="$washingMachine"
            resource="washing-machines"
        />
    </div>
</div>
@endsection

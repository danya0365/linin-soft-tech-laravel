@extends('layouts.app')

@section('template_title')
    {{ $dryerMachine->name ?? 'Show Dryer Machine' }}
@endsection

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    
    {{-- Breadcrumb --}}
    <x-crud.breadcrumb :items="[
        ['label' => 'Admin', 'route' => 'admin'],
        ['label' => 'Dryer Machines', 'route' => 'dryer-machines.index'],
        ['label' => $dryerMachine->name]
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
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Dryer Machine Details</h2>
                    <div class="flex items-center gap-2">
                        <x-ui.button 
                            :href="route('dryer-machines.create-note', ['id' => $dryerMachine->id])"
                            variant="primary"
                            size="sm"
                            icon="fa fa-plus"
                        >
                            Add Note
                        </x-ui.button>
                        <x-ui.button 
                            :href="route('dryer-machines.edit', $dryerMachine->id)"
                            variant="success"
                            size="sm"
                            icon="fa fa-edit"
                        >
                            Edit
                        </x-ui.button>
                        <x-ui.button 
                            :href="route('dryer-machines.index')"
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
                :model="$dryerMachine" 
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
            :model="$dryerMachine"
            resource="dryer-machines"
        />
    </div>
</div>
@endsection

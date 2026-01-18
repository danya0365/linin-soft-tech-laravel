@extends('layouts.app')

@section('template_title')
    {{ $truck->name ?? 'Show Truck' }}
@endsection

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    
    {{-- Breadcrumb --}}
    <x-crud.breadcrumb :items="[
        ['label' => 'Admin', 'route' => 'admin'],
        ['label' => 'Trucks', 'route' => 'trucks.index'],
        ['label' => $truck->name]
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
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Truck Details</h2>
                    <div class="flex items-center gap-2">
                        <x-ui.button 
                            :href="route('trucks.create-note', ['id' => $truck->id])"
                            variant="primary"
                            size="sm"
                            icon="fa fa-plus"
                        >
                            Add Note
                        </x-ui.button>
                        <x-ui.button 
                            :href="route('trucks.edit', $truck->id)"
                            variant="success"
                            size="sm"
                            icon="fa fa-edit"
                        >
                            Edit
                        </x-ui.button>
                        <x-ui.button 
                            :href="route('trucks.index')"
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
                :model="$truck" 
                :fields="[
                    'name' => 'Name',
                    'photo' => 'Photo',
                    'plate_number' => 'Plate Number',
                    'operation_id' => 'Operation ID'
                ]"
            />
        </x-ui.card>
        
        {{-- Notes Section --}}
        <x-crud.notes-section 
            :notes="$notes"
            :tags="$machineTags"
            :selectedTag="$selectedTag"
            :model="$truck"
            resource="trucks"
        />
    </div>
</div>
@endsection

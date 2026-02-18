@extends('layouts.app')
@section('template_title')
    Show Dryer Machine
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => route('admin')],['label' => 'Dryer Machine', 'route' => route('dryer-machines.index')],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
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
                    <x-ui.button :href="route('dryer-machines.edit', $dryerMachine->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('dryer-machines.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Name</p><p class="font-semibold text-gray-900 dark:text-white">{{ $dryerMachine->name ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Photo</p><p class="font-semibold text-gray-900 dark:text-white">{{ $dryerMachine->photo ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Maximum Weight</p><p class="font-semibold text-gray-900 dark:text-white">{{ $dryerMachine->maximum_weight ?? '-' }}</p></div>
        </div>
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
@endsection
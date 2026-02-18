@extends('layouts.app')
@section('template_title')
    Show Truck
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => route('admin')],['label' => 'Truck', 'route' => route('trucks.index')],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
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
                    <x-ui.button :href="route('trucks.edit', $truck->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('trucks.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Name</p><p class="font-semibold text-gray-900 dark:text-white">{{ $truck->name ?? '-' }}</p></div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Photo</p>
                <div class="mt-1">
                    @if($truck->photo)
                        <img src="{{ asset($truck->photo) }}" alt="{{ $truck->name }}" class="max-w-xs rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                    @else
                        <span class="text-gray-400 dark:text-gray-500 italic">No photo available</span>
                    @endif
                </div>
            </div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Plate Number</p><p class="font-semibold text-gray-900 dark:text-white">{{ $truck->plate_number ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Operation Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $truck->operation_id ?? '-' }}</p></div>
        </div>
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
@endsection
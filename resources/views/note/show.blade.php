@extends('layouts.app')
@section('template_title')
    Show Note
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Note', 'route' => 'notes.index'],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Note Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button :href="route('notes.edit', $note->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('notes.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Message</p><p class="font-semibold text-gray-900 dark:text-white">{{ $note->message ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Image Url</p><p class="font-semibold text-gray-900 dark:text-white">{{ $note->image_url ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Cost</p><p class="font-semibold text-gray-900 dark:text-white">{{ $note->cost ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Tags</p><p class="font-semibold text-gray-900 dark:text-white">{{ $note->tags ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Washing Machine Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $note->washing_machine_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Dryer Machine Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $note->dryer_machine_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Truck Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $note->truck_id ?? '-' }}</p></div>
        </div>
    </x-ui.card>
</div>
@endsection
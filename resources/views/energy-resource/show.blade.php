@extends('layouts.app')
@section('template_title')
    Show Energy Resource
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Energy Resource', 'route' => 'energy-resources.index'],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Energy Resource Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button :href="route('energy-resources.edit', $energyResource->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('energy-resources.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            
        </div>
    </x-ui.card>
</div>
@endsection
@extends('layouts.app')
@section('template_title')
    Energy Resource
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Energy Resource']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Energy Resource" :createRoute="route('energy-resource.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Name', 'Actions']"
            :data="$energyResources"
            :columns="['name']"
            resource="energy-resource"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $energyResources->links() }}</div>
</div>
@endsection
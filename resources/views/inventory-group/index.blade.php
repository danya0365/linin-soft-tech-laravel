@extends('layouts.app')
@section('template_title')
    Inventory Group
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Inventory Group']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Inventory Group" :createRoute="route('inventory-groups.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Actions']"
            :data="$inventoryGroups"
            :columns="['']"
            resource="inventory-groups"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $inventoryGroups->links() }}</div>
</div>
@endsection
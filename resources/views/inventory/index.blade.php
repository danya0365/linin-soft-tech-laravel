@extends('layouts.app')
@section('template_title')
    Inventory
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Inventory']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Inventory" :createRoute="route('inventory.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Inventory Group Id', 'Name', 'Unit', 'Total Quantity', 'Remain Quantity', 'Actions']"
            :data="$inventories"
            :columns="['inventory_group_id', 'name', 'unit', 'total_quantity', 'remain_quantity']"
            resource="inventory"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $inventories->links() }}</div>
</div>
@endsection
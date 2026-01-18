@extends('layouts.app')
@section('template_title')
    Inventory Stock Log
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Inventory Stock Log']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Inventory Stock Log" :createRoute="route('inventory-stock-log.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Employee Id', 'Inventory Id', 'Type', 'Quantity', 'Cost', 'Actions']"
            :data="$inventoryStockLogs"
            :columns="['employee_id', 'inventory_id', 'type', 'quantity', 'cost']"
            resource="inventory-stock-log"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $inventoryStockLogs->links() }}</div>
</div>
@endsection
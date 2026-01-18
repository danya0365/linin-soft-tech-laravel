@extends('layouts.app')
@section('template_title')
    Energy Resource Log
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Energy Resource Log']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Energy Resource Log" :createRoute="route('energy-resource-log.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Energy Resource Id', 'Employee Id', 'Value', 'Unit', 'Lot Number', 'Actions']"
            :data="$energyResourceLogs"
            :columns="['energy_resource_id', 'employee_id', 'value', 'unit', 'lot_number']"
            resource="energy-resource-log"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $energyResourceLogs->links() }}</div>
</div>
@endsection
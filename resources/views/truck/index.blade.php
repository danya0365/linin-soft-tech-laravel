@extends('layouts.app')

@section('template_title')
    Truck
@endsection

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    
    {{-- Breadcrumb --}}
    <x-crud.breadcrumb :items="[
        ['label' => 'Admin', 'route' => 'admin'],
        ['label' => 'Trucks']
    ]" />
    
    {{-- Main Card --}}
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header 
                title="Trucks"
                :createRoute="route('trucks.create')"
            />
        </x-slot:header>
        
        {{-- Success Alert --}}
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">
                {{ $message }}
            </x-ui.alert>
        @endif
        
        {{-- Data Table --}}
        <x-crud.data-table 
            :headers="['No', 'Name', 'Photo', 'Plate Number', 'Operation ID', 'Actions']"
            :data="$trucks"
            :columns="['name', 'photo', 'plate_number', 'operation_id']"
            resource="trucks"
            :startIndex="$i"
        />
    </x-ui.card>
    
    {{-- Pagination --}}
    <div class="mt-6">
        {{ $trucks->links() }}
    </div>
</div>
@endsection

@extends('layouts.app')
@section('template_title')
    Note
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Note']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Note" :createRoute="route('note.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Message', 'Image Url', 'Cost', 'Washing Machine Id', 'Dryer Machine Id', 'Truck Id', 'Actions']"
            :data="$notes"
            :columns="['message', 'image_url', 'cost', 'washing_machine_id', 'dryer_machine_id', 'truck_id']"
            resource="note"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $notes->links() }}</div>
</div>
@endsection
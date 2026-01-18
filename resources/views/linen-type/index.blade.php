@extends('layouts.app')
@section('template_title')
    Linen Type
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Linen Type']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Linen Type" :createRoute="route('linen-type.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Name', 'Actions']"
            :data="$linenTypes"
            :columns="['name']"
            resource="linen-type"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $linenTypes->links() }}</div>
</div>
@endsection
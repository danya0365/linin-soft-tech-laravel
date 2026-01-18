@extends('layouts.app')
@section('template_title')
    Linen Product
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Linen Product']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Linen Product" :createRoute="route('linen-product.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Linen Type Id', 'Name', 'Actions']"
            :data="$linenProducts"
            :columns="['linen_type_id', 'name']"
            resource="linen-product"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $linenProducts->links() }}</div>
</div>
@endsection
@extends('layouts.app')
@section('template_title')
    Operation
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Operation']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Operation" :createRoute="route('operations.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Actions']"
            :data="$operations"
            :columns="['']"
            resource="operations"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $operations->links() }}</div>
</div>
@endsection
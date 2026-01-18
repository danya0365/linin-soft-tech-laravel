@extends('layouts.app')
@section('template_title')
    Department
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Department']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Department" :createRoute="route('departments.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Actions']"
            :data="$departments"
            :columns="['']"
            resource="departments"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $departments->links() }}</div>
</div>
@endsection
@extends('layouts.app')
@section('template_title')
    Operation Log
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Operation Log']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Operation Log" :createRoute="route('operation-logs.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Actions']"
            :data="$operationLogs"
            :columns="['']"
            resource="operation-logs"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $operationLogs->links() }}</div>
</div>
@endsection
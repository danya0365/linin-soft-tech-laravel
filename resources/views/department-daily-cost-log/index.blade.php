@extends('layouts.app')
@section('template_title')
    Department Daily Cost Log
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Department Daily Cost Log']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Department Daily Cost Log" :createRoute="route('department-daily-cost-logs.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Actions']"
            :data="$departmentDailyCostLogs"
            :columns="['']"
            resource="department-daily-cost-logs"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $departmentDailyCostLogs->links() }}</div>
</div>
@endsection
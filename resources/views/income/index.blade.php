@extends('layouts.app')
@section('template_title')
    Income
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Income']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Income" :createRoute="route('income.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Type Name', 'Table Name', 'Table Id', 'Amount', 'Actions']"
            :data="$incomes"
            :columns="['type_name', 'table_name', 'table_id', 'amount']"
            resource="income"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $incomes->links() }}</div>
</div>
@endsection
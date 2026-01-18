@extends('layouts.app')
@section('template_title')
    Expense
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Expense']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Expense" :createRoute="route('expenses.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Actions']"
            :data="$expenses"
            :columns="['']"
            resource="expenses"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $expenses->links() }}</div>
</div>
@endsection
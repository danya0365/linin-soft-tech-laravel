@extends('layouts.app')
@section('template_title')
    Customer Operation Daily Summary
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Daily Summary']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Customer Operation Daily Summary" :createRoute="route('customer-operation-daily-summaries.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Customer ID', 'Date', 'Wet', 'Dry', 'Iron', 'Packing', 'Edit Collect', 'Collect', 'Billing', 'Actions']"
            :data="$customerOperationDailySummaries"
            :columns="['customer_id', 'operation_date', 'total_wet_weight', 'total_dry_weight', 'total_iron_piece', 'total_packing_piece', 'total_edit_collect_weight', 'total_collect_weight', 'total_billing_weight']"
            resource="customer-operation-daily-summaries"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $customerOperationDailySummaries->links() }}</div>
</div>
@endsection

@extends('layouts.app')
@section('template_title')
    {{ 'Show Summary' }}
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Daily Summary', 'route' => 'customer-operation-daily-summaries.index'],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Daily Summary Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button :href="route('customer-operation-daily-summaries.edit', $customerOperationDailySummary->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('customer-operation-daily-summaries.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Customer ID</p><p class="font-semibold text-gray-900 dark:text-white">{{ $customerOperationDailySummary->customer_id }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Operation Date</p><p class="font-semibold text-gray-900 dark:text-white">{{ $customerOperationDailySummary->operation_date }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Wet Weight</p><p class="font-semibold text-gray-900 dark:text-white">{{ $customerOperationDailySummary->total_wet_weight }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Dry Weight</p><p class="font-semibold text-gray-900 dark:text-white">{{ $customerOperationDailySummary->total_dry_weight }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Iron Piece</p><p class="font-semibold text-gray-900 dark:text-white">{{ $customerOperationDailySummary->total_iron_piece }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Packing Piece</p><p class="font-semibold text-gray-900 dark:text-white">{{ $customerOperationDailySummary->total_packing_piece }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Edit Collect Weight</p><p class="font-semibold text-gray-900 dark:text-white">{{ $customerOperationDailySummary->total_edit_collect_weight }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Collect Weight</p><p class="font-semibold text-gray-900 dark:text-white">{{ $customerOperationDailySummary->total_collect_weight }}</p></div>
            <div class="md:col-span-2"><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Billing Weight</p><p class="font-semibold text-gray-900 dark:text-white">{{ $customerOperationDailySummary->total_billing_weight }}</p></div>
        </div>
    </x-ui.card>
</div>
@endsection

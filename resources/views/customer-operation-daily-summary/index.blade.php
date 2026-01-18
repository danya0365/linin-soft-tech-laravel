@extends('layouts.app')
@section('template_title')
    Customer Operation Daily Summary
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Customer Operation Daily Summary']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Customer Operation Daily Summary" :createRoute="route('customer-operation-daily-summaries.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        
        <div class="overflow-x-auto rounded-lg shadow">
            <table class="w-full text-sm text-left border-collapse bg-white dark:bg-gray-800">
                <thead class="bg-gray-100 dark:bg-gray-700 border-b-2 border-gray-300 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-20">No</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Detail</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-64">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($customerOperationDailySummaries as $index => $customerOperationDailySummary)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $i + $index + 1 }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                <strong>Customer Id:</strong> {{ $customerOperationDailySummary->customer_id ?? '-' }}<br><strong>Operation Date:</strong> {{ $customerOperationDailySummary->operation_date ?? '-' }}<br><strong>Total Wet Weight:</strong> {{ $customerOperationDailySummary->total_wet_weight ?? '-' }}<br><strong>Total Dry Weight:</strong> {{ $customerOperationDailySummary->total_dry_weight ?? '-' }}<br><strong>Total Iron Piece:</strong> {{ $customerOperationDailySummary->total_iron_piece ?? '-' }}<br><strong>Total Packing Piece:</strong> {{ $customerOperationDailySummary->total_packing_piece ?? '-' }}<br><strong>Total Edit Collect Weight:</strong> {{ $customerOperationDailySummary->total_edit_collect_weight ?? '-' }}<br><strong>Total Collect Weight:</strong> {{ $customerOperationDailySummary->total_collect_weight ?? '-' }}<br><strong>Total Billing Weight:</strong> {{ $customerOperationDailySummary->total_billing_weight ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <x-crud.action-buttons :model="$customerOperationDailySummary" resource="customer-operation-daily-summaries" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa fa-inbox text-4xl text-gray-300 dark:text-gray-600"></i>
                                    <p>No Customer Operation Daily Summary available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    <div class="mt-6">{{ $customerOperationDailySummaries->links() }}</div>
</div>
@endsection
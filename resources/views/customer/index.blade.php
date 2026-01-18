@extends('layouts.app')

@section('template_title')
    Customer
@endsection

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    
    {{-- Breadcrumb --}}
    <x-crud.breadcrumb :items="[
        ['label' => 'Admin', 'route' => 'admin'],
        ['label' => 'ลูกค้า (Customers)']
    ]" />
    
    {{-- Main Card --}}
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header 
                title="Customer"
                :createRoute="route('customers.create')"
            />
        </x-slot:header>
        
        {{-- Success Alert --}}
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">
                {{ $message }}
            </x-ui.alert>
        @endif
        
        {{-- Data Table --}}
        <div class="overflow-x-auto rounded-lg shadow">
            <table class="w-full text-sm text-left border-collapse bg-white dark:bg-gray-800">
                <thead class="bg-gray-100 dark:bg-gray-700 border-b-2 border-gray-300 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Customer Group</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Wet Weight</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Dry Weight</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Billing Weight</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Edit Weight</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Billing Payment</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($customers as $index => $customer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $i + $index + 1 }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $customer->name }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $customer->customerGroup ? $customer->customerGroup->name : '-' }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $customer->total_wet_weight ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $customer->total_dry_weight ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $customer->total_billing_weight ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $customer->total_edit_weight ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $customer->total_billing_payment ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <x-crud.action-buttons 
                                    :model="$customer"
                                    resource="customers"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa fa-inbox text-4xl text-gray-300 dark:text-gray-600"></i>
                                    <p>No customers available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    
    {{-- Pagination --}}
    <div class="mt-6">
        {{ $customers->links() }}
    </div>
</div>
@endsection

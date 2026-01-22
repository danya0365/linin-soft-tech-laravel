@extends('layouts.app')
@section('template_title')
    Customer
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Customer']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Customer" :createRoute="route('customers.create')" />
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
                    @forelse($customers as $index => $customer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $i + $index + 1 }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                <strong>Name:</strong> {{ $customer->name ?? '-' }}<br><strong>Customer Group:</strong> {{ $customer->customerGroup ? $customer->customerGroup->name : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <x-crud.action-buttons :model="$customer" resource="customers" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa fa-inbox text-4xl text-gray-300 dark:text-gray-600"></i>
                                    <p>No Customer available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    <div class="mt-6">{{ $customers->links() }}</div>
</div>
@endsection
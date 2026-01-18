@extends('layouts.app')

@section('template_title')
    {{ $customer->name ?? 'Show Customer' }}
@endsection

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    
    {{-- Breadcrumb --}}
    <x-crud.breadcrumb :items="[
        ['label' => 'Admin', 'route' => 'admin'],
        ['label' => 'Customers', 'route' => 'customers.index'],
        ['label' => $customer->name]
    ]" />
    
    {{-- Detail Card --}}
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Customer Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button 
                        :href="route('customers.edit', $customer->id)"
                        variant="success"
                        size="sm"
                        icon="fa fa-edit"
                    >
                        Edit
                    </x-ui.button>
                    <x-ui.button 
                        :href="route('customers.index')"
                        variant="secondary"
                        size="sm"
                        icon="fa fa-arrow-left"
                    >
                        Back
                    </x-ui.button>
                </div>
            </div>
        </x-slot:header>
        
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Name</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $customer->name }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Customer Group</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $customer->customerGroup ? $customer->customerGroup->name : '-' }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Wet Weight</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $customer->total_wet_weight ?? '-' }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Dry Weight</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $customer->total_dry_weight ?? '-' }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Billing Weight</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $customer->total_billing_weight ?? '-' }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Edit Weight</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $customer->total_edit_weight ?? '-' }}</p>
            </div>
            
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Billing Payment</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $customer->total_billing_payment ?? '-' }}</p>
            </div>
        </div>
    </x-ui.card>
</div>
@endsection

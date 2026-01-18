@extends('layouts.app')

@section('template_title')
    Update Customer
@endsection

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    
    {{-- Breadcrumb --}}
    <x-crud.breadcrumb :items="[
        ['label' => 'Admin', 'route' => 'admin'],
        ['label' => 'Customers', 'route' => 'customers.index'],
        ['label' => 'Edit']
    ]" />
    
    {{-- Validation Errors --}}
    @includeif('partials.errors')
    
    {{-- Edit Form Card --}}
    <x-ui.card>
        <x-slot:header>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Update Customer</h2>
        </x-slot:header>
        
        <form method="POST" action="{{ route('customers.update', $customer->id) }}" role="form" enctype="multipart/form-data">
            {{ method_field('PATCH') }}
            @csrf
            
            @include('customer.form')
            
        </form>
    </x-ui.card>
</div>
@endsection

@extends('layouts.app')
@section('template_title')
    Show Expense
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Expense', 'route' => 'expense.index'],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Expense Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button :href="route('expense.edit', $expense->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('expense.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Type Name</p><p class="font-semibold text-gray-900 dark:text-white">{{ $expense->type_name ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Table Name</p><p class="font-semibold text-gray-900 dark:text-white">{{ $expense->table_name ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Table Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $expense->table_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Amount</p><p class="font-semibold text-gray-900 dark:text-white">{{ $expense->amount ?? '-' }}</p></div>
        </div>
    </x-ui.card>
</div>
@endsection
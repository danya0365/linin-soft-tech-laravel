@extends('layouts.app')
@section('template_title')
    Show Login History
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Login History', 'route' => 'login-history.index'],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Login History Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button :href="route('login-history.edit', $loginHistory->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('login-history.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">User Id</p><p class="font-semibold text-gray-900 dark:text-white">{{ $loginHistory->user_id ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Name</p><p class="font-semibold text-gray-900 dark:text-white">{{ $loginHistory->name ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Email</p><p class="font-semibold text-gray-900 dark:text-white">{{ $loginHistory->email ?? '-' }}</p></div>
        </div>
    </x-ui.card>
</div>
@endsection
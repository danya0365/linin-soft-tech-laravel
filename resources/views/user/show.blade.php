@extends('layouts.app')
@section('template_title')
    Show User
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'User', 'route' => 'users.index'],['label' => 'Details']]" />
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">User Details</h2>
                <div class="flex items-center gap-2">
                    <x-ui.button :href="route('users.edit', $user->id)" variant="success" size="sm" icon="fa fa-edit">Edit</x-ui.button>
                    <x-ui.button :href="route('users.index')" variant="secondary" size="sm" icon="fa fa-arrow-left">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class="grid md:grid-cols-2 gap-6">
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Name</p><p class="font-semibold text-gray-900 dark:text-white">{{ $user->name ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Email</p><p class="font-semibold text-gray-900 dark:text-white">{{ $user->email ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Role</p><p class="font-semibold text-gray-900 dark:text-white">{{ $user->role ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Customer Account</p><p class="font-semibold text-gray-900 dark:text-white">{{ $user->customer_account ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Is Can Access Admin</p><p class="font-semibold text-gray-900 dark:text-white">{{ $user->is_can_access_admin ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Is Can Access Manager</p><p class="font-semibold text-gray-900 dark:text-white">{{ $user->is_can_access_manager ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Is Can Access Supervisor</p><p class="font-semibold text-gray-900 dark:text-white">{{ $user->is_can_access_supervisor ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Is Can Access Customer</p><p class="font-semibold text-gray-900 dark:text-white">{{ $user->is_can_access_customer ?? '-' }}</p></div>
            <div><p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Is Can Access Worker</p><p class="font-semibold text-gray-900 dark:text-white">{{ $user->is_can_access_worker ?? '-' }}</p></div>
        </div>
    </x-ui.card>
</div>
@endsection
@extends('layouts.app')
@section('template_title')
    User
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'User']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="User" :createRoute="route('users.create')" />
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
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $i + $index + 1 }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                <strong>Name:</strong> {{ $user->name ?? '-' }}<br><strong>Email:</strong> {{ $user->email ?? '-' }}<br><strong>Role:</strong> {{ $user->role ?? '-' }}<br><strong>Customer Account:</strong> {{ $user->customer_account ?? '-' }}<br><strong>Is Can Access Admin:</strong> {{ $user->is_can_access_admin ?? '-' }}<br><strong>Is Can Access Manager:</strong> {{ $user->is_can_access_manager ?? '-' }}<br><strong>Is Can Access Supervisor:</strong> {{ $user->is_can_access_supervisor ?? '-' }}<br><strong>Is Can Access Customer:</strong> {{ $user->is_can_access_customer ?? '-' }}<br><strong>Is Can Access Worker:</strong> {{ $user->is_can_access_worker ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <x-crud.action-buttons :model="$user" resource="users" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa fa-inbox text-4xl text-gray-300 dark:text-gray-600"></i>
                                    <p>No User available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    <div class="mt-6">{{ $users->links() }}</div>
</div>
@endsection
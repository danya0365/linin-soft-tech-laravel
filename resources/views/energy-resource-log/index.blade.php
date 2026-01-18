@extends('layouts.app')
@section('template_title')
    Energy Resource Log
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Energy Resource Log']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Energy Resource Log" :createRoute="route('energy-resource-logs.create')" />
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
                    @forelse($energyResourceLogs as $index => $energyResourceLog)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $i + $index + 1 }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                <strong>Energy Resource Id:</strong> {{ $energyResourceLog->energy_resource_id ?? '-' }}<br><strong>Employee Id:</strong> {{ $energyResourceLog->employee_id ?? '-' }}<br><strong>Value:</strong> {{ $energyResourceLog->value ?? '-' }}<br><strong>Unit:</strong> {{ $energyResourceLog->unit ?? '-' }}<br><strong>Lot Number:</strong> {{ $energyResourceLog->lot_number ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <x-crud.action-buttons :model="$energyResourceLog" resource="energy-resource-logs" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa fa-inbox text-4xl text-gray-300 dark:text-gray-600"></i>
                                    <p>No Energy Resource Log available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    <div class="mt-6">{{ $energyResourceLogs->links() }}</div>
</div>
@endsection
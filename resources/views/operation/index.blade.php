@extends('layouts.app')
@section('template_title')
    Operation
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Operation']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Operation" :createRoute="route('operations.create')" />
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
                    @forelse($operations as $index => $operation)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $i + $index + 1 }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                <strong>Operation Type:</strong> {{ $operation->operation_type ?? '-' }}<br><strong>Employee Id:</strong> {{ $operation->employee_id ?? '-' }}<br><strong>Customer Id:</strong> {{ $operation->customer_id ?? '-' }}<br><strong>Wash Employee Id:</strong> {{ $operation->wash_employee_id ?? '-' }}<br><strong>Dry Employee Id:</strong> {{ $operation->dry_employee_id ?? '-' }}<br><strong>Iron Employee Id:</strong> {{ $operation->iron_employee_id ?? '-' }}<br><strong>Packing Employee Id:</strong> {{ $operation->packing_employee_id ?? '-' }}<br><strong>Collect Employee Id:</strong> {{ $operation->collect_employee_id ?? '-' }}<br><strong>Job Case:</strong> {{ $operation->job_case ?? '-' }}<br><strong>Washing Machine Id:</strong> {{ $operation->washing_machine_id ?? '-' }}<br><strong>Dryer Machine Id:</strong> {{ $operation->dryer_machine_id ?? '-' }}<br><strong>Total Wet Weight:</strong> {{ $operation->total_wet_weight ?? '-' }}<br><strong>Total Dry Weight:</strong> {{ $operation->total_dry_weight ?? '-' }}<br><strong>Total Iron Piece:</strong> {{ $operation->total_iron_piece ?? '-' }}<br><strong>Total Packing Piece:</strong> {{ $operation->total_packing_piece ?? '-' }}<br><strong>Colors:</strong> {{ $operation->colors ?? '-' }}<br><strong>Search Tags:</strong> {{ $operation->search_tags ?? '-' }}<br><strong>Status:</strong> {{ $operation->status ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <x-crud.action-buttons :model="$operation" resource="operations" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa fa-inbox text-4xl text-gray-300 dark:text-gray-600"></i>
                                    <p>No Operation available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    <div class="mt-6">{{ $operations->links() }}</div>
</div>
@endsection
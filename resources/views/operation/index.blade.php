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
                <thead class="bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800 border-b-2 border-gray-300 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-20 text-center">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Detail</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-64 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($operations as $index => $operation)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent dark:hover:from-gray-700/50 dark:hover:to-transparent transition-all duration-200">
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm font-semibold">
                                    {{ $i + $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Operation Type:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->operation_type ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Employee Id:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->employee_id ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Customer Id:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->customer_id ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Wash Employee Id:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->wash_employee_id ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Dry Employee Id:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->dry_employee_id ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Iron Employee Id:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->iron_employee_id ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Packing Employee Id:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->packing_employee_id ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Collect Employee Id:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->collect_employee_id ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Job Case:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->job_case ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Washing Machine Id:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->washing_machine_id ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Dryer Machine Id:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->dryer_machine_id ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Total Wet Weight:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->total_wet_weight ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Total Dry Weight:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->total_dry_weight ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Total Iron Piece:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->total_iron_piece ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Total Packing Piece:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->total_packing_piece ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Colors:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->colors ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Search Tags:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->search_tags ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Status:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $operation->status ?? '-' }}</span>
                                        </div>
                                </div>
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
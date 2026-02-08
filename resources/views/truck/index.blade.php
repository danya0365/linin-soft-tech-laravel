@extends('layouts.app')
@section('template_title')
    Truck
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Truck']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Truck" :createRoute="route('trucks.create')" />
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
                    @forelse($trucks as $index => $truck)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent dark:hover:from-gray-700/50 dark:hover:to-transparent transition-all duration-200">
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm font-semibold">
                                    {{ $i + $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Name:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $truck->name ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Photo:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $truck->photo ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Plate Number:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $truck->plate_number ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Operation Id:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $truck->operation_id ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">สถานะ:</span>
                                            @if($truck->service_status === 'available')
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">✅ พร้อมใช้งาน</span>
                                            @else
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">🔧 เสีย</span>
                                            @endif
                                        </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-crud.action-buttons :model="$truck" resource="trucks" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa fa-inbox text-4xl text-gray-300 dark:text-gray-600"></i>
                                    <p>No Truck available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    <div class="mt-6">{{ $trucks->links() }}</div>
</div>
@endsection
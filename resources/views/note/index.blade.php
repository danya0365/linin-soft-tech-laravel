@extends('layouts.app')
@section('template_title')
    Note
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Note']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Note" :createRoute="route('notes.create')" />
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
                    @forelse($notes as $index => $note)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent dark:hover:from-gray-700/50 dark:hover:to-transparent transition-all duration-200">
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm font-semibold">
                                    {{ $i + $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Message:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $note->message ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Image Url:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $note->image_url ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Cost:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $note->cost ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Tags:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $note->tags ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Washing Machine Id:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $note->washing_machine_id ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Dryer Machine Id:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $note->dryer_machine_id ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Truck Id:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $note->truck_id ?? '-' }}</span>
                                        </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-crud.action-buttons :model="$note" resource="notes" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa fa-inbox text-4xl text-gray-300 dark:text-gray-600"></i>
                                    <p>No Note available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    <div class="mt-6">{{ $notes->links() }}</div>
</div>
@endsection
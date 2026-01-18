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
                <thead class="bg-gray-100 dark:bg-gray-700 border-b-2 border-gray-300 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-20">No</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Detail</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-64">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($notes as $index => $note)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $i + $index + 1 }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                <strong>Message:</strong> {{ $note->message ?? '-' }}<br><strong>Image Url:</strong> {{ $note->image_url ?? '-' }}<br><strong>Cost:</strong> {{ $note->cost ?? '-' }}<br><strong>Tags:</strong> {{ $note->tags ?? '-' }}<br><strong>Washing Machine Id:</strong> {{ $note->washing_machine_id ?? '-' }}<br><strong>Dryer Machine Id:</strong> {{ $note->dryer_machine_id ?? '-' }}<br><strong>Truck Id:</strong> {{ $note->truck_id ?? '-' }}
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
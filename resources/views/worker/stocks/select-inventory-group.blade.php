@extends('layouts.worker')

@section('content')

<x-worker.page :breadcrumbs="[['label' => __('Stocks')]]">
    <x-worker.card title="{{ __('Stocks') }}">
        <div class="flex flex-wrap -m-2">
            @php
                $gradients = [
                    'from-blue-500 to-blue-700',
                    'from-green-500 to-green-700',
                    'from-purple-500 to-purple-700',
                    'from-orange-500 to-orange-700',
                    'from-teal-500 to-teal-700',
                    'from-pink-500 to-pink-700',
                ];
            @endphp
            @foreach ($inventoryGroups as $index => $inventoryGroup)
            <x-worker.action-card 
                href="{{ route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventoryGroup->id]) }}"
                icon="{{ $inventoryGroup->icon }}"
                title="{{ __($inventoryGroup->name) }}"
                gradient="{{ $gradients[$index % count($gradients)] }}"
            />
            @endforeach
            
            <x-worker.action-card 
                href="{{ route('worker.stock.inventories-log') }}"
                icon="fa-solid fa-history"
                title="{{ __('ประวัติการเพิ่ม/ลดสต๊อก - Logs') }}"
                gradient="from-gray-600 to-gray-800"
                size="full"
            />
        </div>
    </x-worker.card>
</x-worker.page>

@endsection
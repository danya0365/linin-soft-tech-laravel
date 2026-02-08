@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="{{ __('สต๊อก') }}"
    subtitle="จัดการสินค้าคงคลัง"
    icon="fa-warehouse"
    :breadcrumbs="[
        ['label' => 'Stocks']
    ]"
>
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
    
    <x-menu.grid :columns="3">
        @foreach ($inventoryGroups as $index => $inventoryGroup)
        <x-menu.card 
            href="{{ route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventoryGroup->id]) }}"
            icon="{{ $inventoryGroup->icon }}"
            title="{{ __($inventoryGroup->name) }}"
            subtitle="Stock"
            gradient="{{ $gradients[$index % count($gradients)] }}"
        />
        @endforeach
    </x-menu.grid>
    
    {{-- Full width Logs card --}}
    <div class="mt-4">
        <a href="{{ route('worker.stock.inventories-log') }}" class="group block">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-gray-600 to-gray-800 p-4 sm:p-6 h-24 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div class="flex items-center justify-center h-full gap-4">
                    <i class="fa-solid fa-history text-white text-3xl group-hover:scale-110 transition-transform duration-300"></i>
                    <div class="text-white">
                        <div class="font-bold text-lg">{{ __('ประวัติสต๊อก') }}</div>
                        <div class="text-gray-300 text-sm">Stock Logs</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</x-worker.page>
@endsection
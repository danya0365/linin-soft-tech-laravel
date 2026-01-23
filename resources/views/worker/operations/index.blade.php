@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="{{ __('ปฏิบัติการ') }}"
    subtitle="เลือกประเภทงานที่ต้องการ"
    icon="fa-people-carry-box"
    :breadcrumbs="[
        ['label' => 'Operations']
    ]"
>
    <x-menu.grid :columns="3">
        <x-menu.card 
            href="{{ route('worker.operation.wash') }}"
            icon="fa-solid fa-droplet"
            title="{{ __('ซัก') }}"
            subtitle="Wash"
            gradient="from-blue-500 to-blue-700"
            subtitle-color="text-blue-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.operation.dry') }}"
            icon="fa-solid fa-fire"
            title="{{ __('อบ') }}"
            subtitle="Dry"
            gradient="from-orange-500 to-red-600"
            subtitle-color="text-orange-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.operation.iron') }}"
            icon="fa-solid fa-print"
            title="{{ __('รีด') }}"
            subtitle="Iron"
            gradient="from-purple-500 to-purple-700"
            subtitle-color="text-purple-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.operation.packing') }}"
            icon="fa-solid fa-box"
            title="{{ __('พับแพ็ค') }}"
            subtitle="Packing"
            gradient="from-green-500 to-green-700"
            subtitle-color="text-green-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.operation.collect') }}"
            icon="fa-solid fa-check-to-slot"
            title="{{ __('จัดเก็บ') }}"
            subtitle="Collect"
            gradient="from-teal-500 to-teal-700"
            subtitle-color="text-teal-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.operation.deliver') }}"
            icon="fa-solid fa-truck"
            title="{{ __('จัดส่ง') }}"
            subtitle="Deliver"
            gradient="from-indigo-500 to-indigo-700"
            subtitle-color="text-indigo-100"
        />
    </x-menu.grid>
    
    {{-- Full width card for In Progress --}}
    <div class="mt-4">
        <a href="{{ route('worker.operation.in-progress') }}" class="group block">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-gray-600 to-gray-800 p-4 sm:p-6 h-24 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div class="flex items-center justify-center h-full gap-4">
                    <i class="fa-solid fa-hourglass text-white text-3xl group-hover:scale-110 transition-transform duration-300"></i>
                    <div class="text-white">
                        <div class="font-bold text-lg">{{ __('งานที่ยังไม่จบ') }}</div>
                        <div class="text-gray-300 text-sm">In Progress Operations</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</x-worker.page>
@endsection
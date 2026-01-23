@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="{{ __('พลังงาน') }}"
    subtitle="บันทึกการใช้พลังงานและทรัพยากร"
    icon="fa-bolt"
    :breadcrumbs="[
        ['label' => 'Energy Resource']
    ]"
>
    <x-menu.grid :columns="3">
        <x-menu.card 
            href="{{ route('worker.energy-resource.log.select-energy-resource', ['energyResourceVarName' => 'water']) }}"
            icon="fa-solid fa-water"
            title="{{ __('น้ำ') }}"
            subtitle="Water"
            gradient="from-cyan-500 to-blue-600"
            subtitle-color="text-cyan-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.energy-resource.log.select-energy-resource', ['energyResourceVarName' => 'electricity']) }}"
            icon="fa-solid fa-bolt"
            title="{{ __('ไฟฟ้า') }}"
            subtitle="Electricity"
            gradient="from-yellow-400 to-orange-500"
            subtitle-color="text-yellow-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.energy-resource.log.select-energy-resource', ['energyResourceVarName' => 'gas']) }}"
            icon="fa-solid fa-fire-flame-simple"
            title="{{ __('แก๊ส') }}"
            subtitle="Gas"
            gradient="from-red-500 to-red-700"
            subtitle-color="text-red-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.energy-resource.log.select-energy-resource', ['energyResourceVarName' => 'biomass']) }}"
            icon="fa-brands fa-pagelines"
            title="{{ __('ชีวมวล') }}"
            subtitle="Biomass"
            gradient="from-green-500 to-green-700"
            subtitle-color="text-green-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.energy-resource.log.select-energy-resource', ['energyResourceVarName' => 'chemical']) }}"
            icon="fa-solid fa-oil-can"
            title="{{ __('เคมี') }}"
            subtitle="Chemical"
            gradient="from-purple-500 to-purple-700"
            subtitle-color="text-purple-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.energy-resource.log.select-energy-resource', ['energyResourceVarName' => 'petrol']) }}"
            icon="fa-solid fa-gas-pump"
            title="{{ __('น้ำมันรถ') }}"
            subtitle="Petrol"
            gradient="from-slate-600 to-slate-800"
            subtitle-color="text-slate-100"
        />
    </x-menu.grid>
    
    {{-- Full width Logs card --}}
    <div class="mt-4">
        <a href="{{ route('worker.energy-resource.logs') }}" class="group block">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-gray-600 to-gray-800 p-4 sm:p-6 h-24 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div class="flex items-center justify-center h-full gap-4">
                    <i class="fa-solid fa-history text-white text-3xl group-hover:scale-110 transition-transform duration-300"></i>
                    <div class="text-white">
                        <div class="font-bold text-lg">{{ __('ประวัติการบันทึก') }}</div>
                        <div class="text-gray-300 text-sm">Energy Logs</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</x-worker.page>
@endsection
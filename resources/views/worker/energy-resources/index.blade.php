@extends('layouts.worker')

@section('content')

<x-worker.page :breadcrumbs="[['label' => __('Energy Resource')]]">
    <x-worker.card title="{{ __('Energy Resource') }}">
        <div class="flex flex-wrap -m-2">
            <x-worker.action-card 
                href="{{ route('worker.energy-resource.log.select-energy-resource', ['energyResourceVarName' => 'water']) }}"
                icon="fa-solid fa-water"
                title="{{ __('น้ำ - Water') }}"
                gradient="from-cyan-500 to-blue-600"
            />
            
            <x-worker.action-card 
                href="{{ route('worker.energy-resource.log.select-energy-resource', ['energyResourceVarName' => 'electricity']) }}"
                icon="fa fa-bolt"
                title="{{ __('ไฟฟ้า - Electricity') }}"
                gradient="from-yellow-400 to-orange-500"
            />
            
            <x-worker.action-card 
                href="{{ route('worker.energy-resource.log.select-energy-resource', ['energyResourceVarName' => 'gas']) }}"
                icon="fa-solid fa-fire-flame-simple"
                title="{{ __('แก๊ส - Gas') }}"
                gradient="from-red-500 to-red-700"
            />
            
            <x-worker.action-card 
                href="{{ route('worker.energy-resource.log.select-energy-resource', ['energyResourceVarName' => 'biomass']) }}"
                icon="fa-brands fa-pagelines"
                title="{{ __('ชีวมวล - Biomass') }}"
                gradient="from-green-500 to-green-700"
            />
            
            <x-worker.action-card 
                href="{{ route('worker.energy-resource.log.select-energy-resource', ['energyResourceVarName' => 'chemical']) }}"
                icon="fa-solid fa-oil-can"
                title="{{ __('เคมี - Chemical') }}"
                gradient="from-purple-500 to-purple-700"
            />
            
            <x-worker.action-card 
                href="{{ route('worker.energy-resource.log.select-energy-resource', ['energyResourceVarName' => 'petrol']) }}"
                icon="fa-solid fa-gas-pump"
                title="{{ __('น้ำมันรถ - Petrol') }}"
                gradient="from-slate-600 to-slate-800"
            />
            
            <x-worker.action-card 
                href="{{ route('worker.energy-resource.logs') }}"
                icon="fa-solid fa-history"
                title="{{ __('ประวัติการบันทึก - Logs') }}"
                gradient="from-gray-600 to-gray-800"
                size="full"
            />
        </div>
    </x-worker.card>
</x-worker.page>

@endsection
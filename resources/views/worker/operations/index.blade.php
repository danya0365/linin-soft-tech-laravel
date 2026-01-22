@extends('layouts.worker')

@section('content')

<x-worker.page :breadcrumbs="[['label' => __('ปฏิบัติการ')]]">
    <x-worker.card title="{{ __('ปฏิบัติการ') }}">
        <div class="flex flex-wrap -m-2">
            <x-worker.action-card 
                href="{{ route('worker.operation.wash') }}"
                icon="fa-solid fa-droplet"
                title="{{ __('ซัก - Wash') }}"
                gradient="from-blue-500 to-blue-700"
            />
            
            <x-worker.action-card 
                href="{{ route('worker.operation.dry') }}"
                icon="fa-solid fa-fire"
                title="{{ __('อบ - Dry') }}"
                gradient="from-orange-500 to-red-600"
            />
            
            <x-worker.action-card 
                href="{{ route('worker.operation.iron') }}"
                icon="fa-solid fa-print"
                title="{{ __('รีด - Iron') }}"
                gradient="from-purple-500 to-purple-700"
            />
            
            <x-worker.action-card 
                href="{{ route('worker.operation.packing') }}"
                icon="fa-solid fa-people-carry-box"
                title="{{ __('พับแพ็ค - Packing') }}"
                gradient="from-green-500 to-green-700"
            />
            
            <x-worker.action-card 
                href="{{ route('worker.operation.collect') }}"
                icon="fa-solid fa-check-to-slot"
                title="{{ __('จัดเก็บ - Collect') }}"
                gradient="from-teal-500 to-teal-700"
            />
            
            <x-worker.action-card 
                href="{{ route('worker.operation.deliver') }}"
                icon="fa-solid fa-truck"
                title="{{ __('จัดส่ง - Deliver') }}"
                gradient="from-indigo-500 to-indigo-700"
            />
            
            <x-worker.action-card 
                href="{{ route('worker.operation.in-progress') }}"
                icon="fa-solid fa-hourglass"
                title="{{ __('ปฎิบัติการที่ยังไม่จบงาน - In Progress Operation') }}"
                gradient="from-gray-600 to-gray-800"
                size="full"
            />
        </div>
    </x-worker.card>
</x-worker.page>

@endsection
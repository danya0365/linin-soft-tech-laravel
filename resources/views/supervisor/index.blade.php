@extends('layouts.supervisor')

@section('content')
<x-supervisor.page 
    title="{{ __('Supervisor Menu') }}"
    subtitle="จัดการข้อมูลลูกค้า แผนก และรายงานสถิติ"
    icon="fa-user-tie"
>
    <x-menu.grid :columns="3">
        <x-menu.card 
            href="{{ route('supervisor.customer') }}"
            icon="fa-solid fa-hospital"
            title="{{ __('ลูกค้า') }}"
            subtitle="Customer"
            gradient="from-emerald-500 to-teal-600"
            subtitle-color="text-emerald-100"
        />
        
        <x-menu.card 
            href="{{ route('supervisor.department') }}"
            icon="fa-solid fa-users-rectangle"
            title="{{ __('แผนก') }}"
            subtitle="Department"
            gradient="from-cyan-500 to-blue-600"
            subtitle-color="text-cyan-100"
        />
        
        <x-menu.card 
            href="{{ route('supervisor.report') }}"
            icon="fa-solid fa-chart-pie"
            title="{{ __('รายงานสถิติ') }}"
            subtitle="Report"
            gradient="from-amber-400 to-orange-500"
            subtitle-color="text-amber-100"
        />
    </x-menu.grid>
</x-supervisor.page>
@endsection
@extends('layouts.manager')

@section('content')
<x-menu.page-layout 
    title="{{ __('Manager Menu') }}"
    subtitle="ดูรายงานสถิติและวิเคราะห์ข้อมูล"
    icon="fa-briefcase"
    icon-color="text-indigo-600"
    :breadcrumbs="[['label' => 'Manager']]"
>
    <x-menu.grid :columns="2">
        <x-menu.card 
            href="{{ route('manager.report') }}"
            icon="fa-solid fa-chart-pie"
            title="{{ __('รายงานสถิติ') }}"
            subtitle="Report Overview"
            gradient="from-indigo-600 to-blue-800"
            subtitle-color="text-indigo-200"
        >
            <x-slot:badge>
                <span class="px-3 py-1 bg-white/20 rounded-full text-xs text-white font-medium">
                    <i class="fa fa-star mr-1"></i> Overview
                </span>
            </x-slot:badge>
        </x-menu.card>
        
        <x-menu.card 
            href="{{ route('manager.report.filter') }}"
            icon="fa-solid fa-calendar-days"
            title="{{ __('สถิติตามวันที่') }}"
            subtitle="Day Report"
            gradient="from-purple-600 to-violet-800"
            subtitle-color="text-purple-200"
        >
            <x-slot:badge>
                <span class="px-3 py-1 bg-white/20 rounded-full text-xs text-white font-medium">
                    <i class="fa fa-calendar mr-1"></i> Filter
                </span>
            </x-slot:badge>
        </x-menu.card>
    </x-menu.grid>
</x-menu.page-layout>
@endsection
@extends('layouts.app')

@section('content')
<x-menu.page-layout 
    title="{{ __('Home Menu') }}"
    subtitle="เลือกเมนูตามสิทธิ์การใช้งานของคุณ"
    icon="fa-home"
    icon-color="text-emerald-600"
    :breadcrumbs="[]"
>
    @if (session('status'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <x-menu.grid :columns="2">
        @if (Auth::user()->isWorker())
        <x-menu.card 
            href="{{ route('worker') }}"
            icon="fa-solid fa-people-line"
            title="{{ __('พนักงาน') }}"
            subtitle="Worker"
            gradient="from-blue-500 to-blue-700"
            subtitle-color="text-blue-100"
        />
        @endif
        
        @if (Auth::user()->isUserCustomer())
        <x-menu.card 
            href="{{ route('user-customer') }}"
            icon="fa-solid fa-hospital"
            title="{{ __('ลูกค้า') }}"
            subtitle="Customer"
            gradient="from-emerald-500 to-teal-600"
            subtitle-color="text-emerald-100"
        />
        @endif
        
        @if (Auth::user()->isSupervisor())
        <x-menu.card 
            href="{{ route('supervisor') }}"
            icon="fa-solid fa-people-roof"
            title="{{ __('ผู้คุม') }}"
            subtitle="Supervisor"
            gradient="from-amber-400 to-orange-500"
            subtitle-color="text-amber-100"
        />
        @endif
        
        @if (Auth::user()->isManager())
        <x-menu.card 
            href="{{ route('manager') }}"
            icon="fa-solid fa-user-tie"
            title="{{ __('ผู้จัดการ') }}"
            subtitle="Manager"
            gradient="from-purple-500 to-violet-700"
            subtitle-color="text-purple-100"
        />
        @endif
        
        @if (Auth::user()->isAdmin())
        <x-menu.card 
            href="{{ route('admin') }}"
            icon="fa-solid fa-gears"
            title="{{ __('ผู้ควบคุม') }}"
            subtitle="Admin"
            gradient="from-slate-600 to-gray-800"
            subtitle-color="text-slate-300"
        />
        @endif
    </x-menu.grid>
</x-menu.page-layout>
@endsection

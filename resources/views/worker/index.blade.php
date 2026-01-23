@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="{{ __('Worker Menu') }}"
    subtitle="เลือกเมนูที่ต้องการเข้าใช้งาน"
    icon="fa-th-large"
>
    <x-menu.grid :columns="3">
        <x-menu.card 
            href="{{ route('worker.product') }}"
            icon="fa-solid fa-shirt"
            title="{{ __('สินค้า') }}"
            subtitle="Product"
            gradient="from-blue-500 to-blue-700"
            subtitle-color="text-blue-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.customer') }}"
            icon="fa-solid fa-hospital"
            title="{{ __('ลูกค้า') }}"
            subtitle="Customer"
            gradient="from-amber-400 to-orange-500"
            subtitle-color="text-amber-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.operation') }}"
            icon="fa-solid fa-people-carry-box"
            title="{{ __('ปฏิบัติการ') }}"
            subtitle="Operations"
            gradient="from-rose-500 to-red-600"
            subtitle-color="text-rose-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.energy-resource') }}"
            icon="fa-solid fa-bolt"
            title="{{ __('พลังงาน') }}"
            subtitle="Energy Resource"
            gradient="from-emerald-500 to-green-600"
            subtitle-color="text-emerald-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.employee') }}"
            icon="fa-solid fa-users"
            title="{{ __('พนักงาน') }}"
            subtitle="Employee"
            gradient="from-cyan-500 to-blue-500"
            subtitle-color="text-cyan-100"
        />
        
        <x-menu.card 
            href="{{ route('worker.stock') }}"
            icon="fa-solid fa-warehouse"
            title="{{ __('สต๊อก') }}"
            subtitle="Stock"
            gradient="from-violet-500 to-purple-600"
            subtitle-color="text-violet-100"
        />
    </x-menu.grid>
</x-worker.page>
@endsection
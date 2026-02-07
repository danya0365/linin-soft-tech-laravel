@extends('layouts.admin')

@section('content')
<x-menu.page-layout 
    title="{{ __('Admin Menu') }}"
    subtitle="จัดการข้อมูลหลักของระบบ"
    icon="fa-cog"
    icon-color="text-slate-600"
>
    <!-- Section: Users & Customers -->
    <x-menu.section title="ผู้ใช้งาน & ลูกค้า" icon="fa-users" icon-color="text-blue-500">
        <x-menu.card 
            href="{{ route('users.index') }}"
            icon="fa-solid fa-people-group"
            title="{{ __('ไอดีล็อกอิน') }}"
            subtitle="Users"
            gradient="from-blue-500 to-blue-700"
            subtitle-color="text-blue-200"
        />
        
        <x-menu.card 
            href="{{ route('customer-groups.index') }}"
            icon="fa-solid fa-building-user"
            title="{{ __('กลุ่มลูกค้า') }}"
            subtitle="Customer Groups"
            gradient="from-emerald-500 to-teal-600"
            subtitle-color="text-emerald-200"
        />
        
        <x-menu.card 
            href="{{ route('customers.index') }}"
            icon="fa-solid fa-hospital"
            title="{{ __('ลูกค้า') }}"
            subtitle="Customers"
            gradient="from-cyan-500 to-blue-600"
            subtitle-color="text-cyan-200"
        />
    </x-menu.section>

    <!-- Section: Employees -->
    <x-menu.section title="พนักงาน" icon="fa-id-badge" icon-color="text-orange-500">
        <x-menu.card 
            href="{{ route('departments.index') }}"
            icon="fa-solid fa-people-roof"
            title="{{ __('แผนกพนักงาน') }}"
            subtitle="Departments"
            gradient="from-amber-400 to-orange-500"
            subtitle-color="text-amber-100"
        />
        
        <x-menu.card 
            href="{{ route('employees.index') }}"
            icon="fa-solid fa-person-digging"
            title="{{ __('พนักงาน') }}"
            subtitle="Employees"
            gradient="from-orange-500 to-red-500"
            subtitle-color="text-orange-200"
        />
    </x-menu.section>

    <!-- Section: Products -->
    <x-menu.section title="สินค้า" icon="fa-shirt" icon-color="text-violet-500">
        <x-menu.card 
            href="{{ route('linen-types.index') }}"
            icon="fa-solid fa-layer-group"
            title="{{ __('ชนิดผ้า') }}"
            subtitle="Linen Types"
            gradient="from-violet-500 to-purple-600"
            subtitle-color="text-violet-200"
        />
        
        <x-menu.card 
            href="{{ route('linen-products.index') }}"
            icon="fa-solid fa-shirt"
            title="{{ __('ผ้า') }}"
            subtitle="Linen Products"
            gradient="from-purple-500 to-pink-600"
            subtitle-color="text-purple-200"
        />
    </x-menu.section>

    <!-- Section: Machines & Vehicles -->
    <x-menu.section title="เครื่องจักร & ยานพาหนะ" icon="fa-cogs" icon-color="text-slate-500">
        <x-menu.card 
            href="{{ route('washing-machines.index') }}"
            icon="fa-solid fa-soap"
            title="{{ __('เครื่องซักผ้า') }}"
            subtitle="Washing Machines"
            gradient="from-sky-500 to-blue-600"
            subtitle-color="text-sky-200"
        />
        
        <x-menu.card 
            href="{{ route('dryer-machines.index') }}"
            icon="fa-solid fa-fire"
            title="{{ __('เครื่องอบผ้า') }}"
            subtitle="Dryer Machines"
            gradient="from-rose-500 to-red-600"
            subtitle-color="text-rose-200"
        />
        
        <x-menu.card 
            href="{{ route('trucks.index') }}"
            icon="fa-solid fa-truck"
            title="{{ __('รถบรรทุก') }}"
            subtitle="Trucks"
            gradient="from-slate-500 to-gray-700"
            subtitle-color="text-slate-300"
        />
    </x-menu.section>
</x-menu.page-layout>
@endsection
@extends('layouts.supervisor')

@section('content')
<x-supervisor.page 
    title="{{ __('Department') }}"
    subtitle="จัดการค่าใช้จ่ายรายวันของแผนก"
    icon="fa-users-rectangle"
    :breadcrumbs="[
        ['label' => 'Department']
    ]"
>
    <x-menu.grid :columns="2">
        <x-menu.card 
            href="{{ route('supervisor.department.submit-daily-expense') }}"
            icon="fa-solid fa-money-bill"
            title="{{ __('เพิ่มค่าใช้จ่ายรายวัน') }}"
            subtitle="Daily Expense"
            gradient="from-indigo-500 to-blue-600"
            subtitle-color="text-indigo-100"
        />
        
        <x-menu.card 
            href="{{ route('supervisor.department.daily-expense-log') }}"
            icon="fa-solid fa-history"
            title="{{ __('ประวัติค่าใช้จ่ายรายวัน') }}"
            subtitle="Daily Expense Logs"
            gradient="from-slate-500 to-slate-700"
            subtitle-color="text-slate-100"
        />
    </x-menu.grid>
</x-supervisor.page>
@endsection
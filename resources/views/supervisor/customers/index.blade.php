@extends('layouts.supervisor')

@section('content')
<x-supervisor.page 
    title="{{ __('Customer') }}"
    subtitle="จัดการข้อมูลบิลและประวัติรายรับของลูกค้า"
    icon="fa-hospital"
    :breadcrumbs="[
        ['label' => __('Customer')]
    ]"
>
    <x-menu.grid :columns="2">
        <x-menu.card 
            href="{{ route('supervisor.customer.new-billing') }}"
            icon="fa-solid fa-money-bill"
            title="{{ __('เพิ่มบิลรายรับ') }}"
            subtitle="New Income Billing"
            gradient="from-emerald-500 to-green-600"
            subtitle-color="text-emerald-100"
        />
        
        <x-menu.card 
            href="{{ route('supervisor.customer.billing-logs') }}"
            icon="fa-solid fa-history"
            title="{{ __('ประวัติบิลรายรับ') }}"
            subtitle="Income Billing Logs"
            gradient="from-teal-500 to-teal-700"
            subtitle-color="text-teal-100"
        />
    </x-menu.grid>
</x-supervisor.page>
@endsection
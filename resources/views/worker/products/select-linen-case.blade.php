@extends('layouts.worker')

@section('content')
<x-menu.page-layout 
    title="{{ __('เคสงาน') }}"
    subtitle="เลือกประเภทเคสงานที่ต้องการดู"
    icon="fa-layer-group"
    icon-color="text-blue-600"
    :breadcrumbs="[
        ['label' => 'Worker', 'url' => route('worker')],
        ['label' => 'สินค้า', 'url' => route('worker.product')],
        ['label' => 'เลือกเคสงาน']
    ]"
>
    @php
        $gradientMap = [
            'bg-primary' => 'from-blue-500 to-blue-700',
            'bg-success' => 'from-green-500 to-green-700',
            'bg-warning' => 'from-yellow-500 to-orange-500',
            'bg-danger' => 'from-red-500 to-red-700',
            'bg-info' => 'from-cyan-500 to-cyan-700',
            'bg-secondary' => 'from-gray-500 to-gray-700',
            'bg-dark' => 'from-gray-700 to-gray-900',
            'bg-light' => 'from-slate-400 to-slate-600',
        ];
    @endphp
    
    <x-menu.grid :columns="2">
        @foreach ($operationLinenCases as $operationLinenCase)
        @php
            $gradient = $gradientMap[$operationLinenCase['bg_css_class']] ?? 'from-blue-500 to-blue-700';
        @endphp
        <x-menu.card 
            href="{{ route('worker.product.get-operations-by-linen-case', ['linenCase' => $operationLinenCase['var']]) }}"
            icon="bi {{ $operationLinenCase['icon'] }}"
            title="{{ $operationLinenCase['name'] }}"
            subtitle="Linen Case"
            gradient="{{ $gradient }}"
        />
        @endforeach
    </x-menu.grid>
</x-menu.page-layout>
@endsection
@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="เลือกเคสงาน" 
    subtitle="Select Linen Case" 
    icon="fa-solid fa-layer-group"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.dry.select-employee')],
        ['label' => 'ลูกค้า: ' . $operation['customer']['name'], 'route' => route('worker.operation.dry.select-customer', ['operationId' => $operation['id']])],
        ['label' => 'เครื่องอบผ้า: ' . $operation['dryer_machine']['name'], 'route' => route('worker.operation.dry.select-dryer-machine', ['operationId' => $operation['id']])],
        ['label' => 'เลือกเคสงาน']
    ]"
>
    @php
        $colorMap = [
            'bg-primary' => [
                'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                'border' => 'border-blue-200 dark:border-blue-800',
                'text' => 'text-blue-600 dark:text-blue-400',
                'hover_border' => 'hover:border-blue-400',
                'icon_bg' => 'bg-blue-100 dark:bg-blue-800'
            ],
            'bg-success' => [
                'bg' => 'bg-green-50 dark:bg-green-900/20',
                'border' => 'border-green-200 dark:border-green-800',
                'text' => 'text-green-600 dark:text-green-400',
                'hover_border' => 'hover:border-green-400',
                'icon_bg' => 'bg-green-100 dark:bg-green-800'
            ],
            'bg-warning' => [
                'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
                'border' => 'border-yellow-200 dark:border-yellow-800',
                'text' => 'text-yellow-600 dark:text-yellow-400',
                'hover_border' => 'hover:border-yellow-400',
                'icon_bg' => 'bg-yellow-100 dark:bg-yellow-800'
            ],
            'bg-danger' => [
                'bg' => 'bg-red-50 dark:bg-red-900/20',
                'border' => 'border-red-200 dark:border-red-800',
                'text' => 'text-red-600 dark:text-red-400',
                'hover_border' => 'hover:border-red-400',
                'icon_bg' => 'bg-red-100 dark:bg-red-800'
            ],
            'bg-info' => [
                'bg' => 'bg-cyan-50 dark:bg-cyan-900/20',
                'border' => 'border-cyan-200 dark:border-cyan-800',
                'text' => 'text-cyan-600 dark:text-cyan-400',
                'hover_border' => 'hover:border-cyan-400',
                'icon_bg' => 'bg-cyan-100 dark:bg-cyan-800'
            ],
            'bg-secondary' => [
                'bg' => 'bg-gray-50 dark:bg-gray-800',
                'border' => 'border-gray-200 dark:border-gray-700',
                'text' => 'text-gray-600 dark:text-gray-400',
                'hover_border' => 'hover:border-gray-400',
                'icon_bg' => 'bg-gray-200 dark:bg-gray-700'
            ],
            'bg-dark' => [
                'bg' => 'bg-slate-50 dark:bg-slate-800',
                'border' => 'border-slate-300 dark:border-slate-700',
                'text' => 'text-slate-700 dark:text-slate-300',
                'hover_border' => 'hover:border-slate-500',
                'icon_bg' => 'bg-slate-200 dark:bg-slate-700'
            ],
            'bg-light' => [
                'bg' => 'bg-white dark:bg-gray-800',
                'border' => 'border-gray-200 dark:border-gray-700',
                'text' => 'text-gray-600 dark:text-gray-400',
                'hover_border' => 'hover:border-gray-400',
                'icon_bg' => 'bg-gray-100 dark:bg-gray-700'
            ],
        ];
    @endphp

    <x-worker.card title="เคสงาน">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($operationLinenCases as $operationLinenCase)
            @php
                $colors = $colorMap[$operationLinenCase['bg_css_class']] ?? $colorMap['bg-secondary'];
            @endphp
            <a href="{{ route('worker.operation.dry.set-select-linen-case', ['operationId' => $operation['id'], 'linenCase' => $operationLinenCase['var'], 'operationLinenProductId' => $operationLinenProduct->id]) }}" 
               class="group relative flex flex-col items-center justify-center p-6 rounded-xl border-2 shadow-sm hover:shadow-md transition-all duration-200 {{ $colors['bg'] }} {{ $colors['border'] }} {{ $colors['hover_border'] }}"
               style="min-height: 180px">
                
                <div class="mb-4 transform group-hover:scale-110 transition-transform duration-200">
                    <div class="w-20 h-20 flex items-center justify-center rounded-full {{ $colors['icon_bg'] }} {{ $colors['text'] }}">
                        <i class="bi {{ $operationLinenCase['icon'] }} text-4xl"></i>
                    </div>
                </div>
                
                <div class="text-center">
                    <h4 class="text-lg font-bold {{ $colors['text'] }}">
                        {{ $operationLinenCase['name'] }}
                    </h4>
                </div>
            </a>
            @endforeach
        </div>
    </x-worker.card>
</x-worker.page>
@endsection
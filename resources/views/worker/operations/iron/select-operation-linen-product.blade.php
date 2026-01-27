@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="แก้ไขหรือลบรายการ" 
    subtitle="Edit or Delete Items" 
    icon="fa-solid fa-pen-to-square"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.iron.select-employee')],
        ['label' => 'ลูกค้า: ' . $operation['customer']['name'], 'route' => route('worker.operation.iron.select-customer', ['operationId' => $operation['id']])],
        ['label' => 'สรุปข้อมูลการรีด', 'route' => route('worker.operation.iron.employee-summary', ['operationId' => $operation['id']])],
        ['label' => 'แก้ไข/ลบ']
    ]"
>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($operationLinenProducts as $operationLinenProduct)
        <x-worker.card>
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $operationLinenProduct['linen_case'] ? $operationLinenProduct['linen_case']['name'] : 'ยังไม่ได้เลือก' }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $operationLinenProduct['linen_product'] ? $operationLinenProduct['linen_product']['name'] : 'ยังไม่ได้เลือก' }}
                    </p>
                </div>
                @if($operationLinenProduct['color'])
                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <span class="w-2 h-2 rounded-full mr-1" style="background-color: {{ $operationLinenProduct['color'] }}"></span>
                    {{ $operationLinenProduct['color'] }}
                </span>
                @endif
            </div>

            <div class="mb-6">
                <div class="flex justify-between items-center text-sm mb-1">
                    <span class="text-gray-500">จำนวนชิ้น</span>
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ $operationLinenProduct['iron_piece'] ? $operationLinenProduct['iron_piece'] : '-' }} ชิ้น
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('worker.operation.iron.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}" 
                   class="flex items-center justify-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-amber-700 bg-amber-100 hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-900/50 transition-colors">
                    <i class="fa-solid fa-pen mr-2"></i> แก้ไข
                </a>
                <a href="{{ route('worker.operation.iron.delete-operation-linen-product', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}" 
                   class="flex items-center justify-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 transition-colors">
                    <i class="fa-solid fa-trash mr-2"></i> ลบ
                </a>
            </div>
        </x-worker.card>
        @endforeach
    </div>
</x-worker.page>
@endsection
@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="เลือกที่จะแก้ไขหรือลบ" 
    subtitle="Select to Edit or Delete" 
    icon="fa-solid fa-pen-to-square"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.iron.select-employee')],
        ['label' => 'ลูกค้า: ' . $operation['customer']['name'], 'route' => route('worker.operation.iron.select-customer', ['operationId' => $operation['id']])],
        ['label' => 'สรุปข้อมูลการรีด', 'route' => route('worker.operation.iron.employee-summary', ['operationId' => $operation['id']])],
        ['label' => 'แก้ไขรายการ']
    ]"
>
    <div class="space-y-6">
        @foreach ($operationLinenProducts as $operationLinenProduct)
        <x-worker.card>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="space-y-2 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            {{ $operationLinenProduct['linen_case'] ? $operationLinenProduct['linen_case']['name'] : 'ยังไม่ได้เลือก' }}
                        </span>
                    </div>
                    
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $operationLinenProduct['linen_product'] ? $operationLinenProduct['linen_product']['name'] : 'ยังไม่ได้เลือก' }}
                    </h4>
                    
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-shirt"></i>
                            <span>{{ $operationLinenProduct['iron_piece'] ? $operationLinenProduct['iron_piece'] : '0' }} ชิ้น</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-palette"></i>
                            <span style="color: {{ $operationLinenProduct['color'] ?? 'inherit' }}">
                                {{ $operationLinenProduct['color'] ? $operationLinenProduct['color'] : 'ไม่มีสี' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <a href="{{ route('worker.operation.iron.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}" 
                       class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                        <i class="fa-solid fa-pen mr-2"></i>
                        แก้ไข
                    </a>
                    
                    <a href="{{ route('worker.operation.iron.delete-operation-linen-product', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}" 
                       class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i class="fa-solid fa-trash mr-2"></i>
                        ลบ
                    </a>
                </div>
            </div>
        </x-worker.card>
        @endforeach
    </div>
</x-worker.page>
@endsection
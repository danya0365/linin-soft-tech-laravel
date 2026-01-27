@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="เลือกรายการที่ต้องการแก้ไข" 
    subtitle="Select Item to Edit" 
    icon="fa-solid fa-pen-to-square"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.collect.select-employee')],
        ['label' => 'ลูกค้า: ' . $operation['customer']['name'], 'route' => route('worker.operation.collect.select-customer', ['operationId' => $operation['id']])],
        ['label' => 'สรุปข้อมูลการจัดเก็บ', 'route' => route('worker.operation.collect.employee-summary', ['operationId' => $operation['id']])],
        ['label' => 'แก้ไขรายการ']
    ]"
>
    <div class="space-y-4">
        @foreach ($operationLinenProducts as $operationLinenProduct)
        <x-worker.card>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-2 flex-grow">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            {{ $operationLinenProduct['linen_case'] ? $operationLinenProduct['linen_case']['name'] : 'ยังไม่ได้เลือก Case' }}
                        </span>
                        @if($operationLinenProduct['color'])
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border" style="color: {{ $operationLinenProduct['color'] }}; border-color: {{ $operationLinenProduct['color'] }}">
                                {{ $operationLinenProduct['color'] }}
                            </span>
                        @endif
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $operationLinenProduct['linen_product'] ? $operationLinenProduct['linen_product']['name'] : 'ยังไม่ได้เลือกสินค้า' }}
                    </h3>

                    <div class="flex gap-4 text-sm text-gray-600 dark:text-gray-400">
                        <div>
                            <span class="font-medium">น้ำหนัก:</span> 
                            {{ $operationLinenProduct['collect_weight'] ? $operationLinenProduct['collect_weight'] : '-' }} กก.
                        </div>
                        <div>
                            <span class="font-medium">จำนวน:</span> 
                            {{ $operationLinenProduct['collect_pack'] ? $operationLinenProduct['collect_pack'] : '-' }} แพ็ค
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 w-full md:w-auto">
                    <a href="{{ route('worker.operation.collect.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}" 
                       class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fa-solid fa-pen mr-2"></i> แก้ไข
                    </a>
                    <a href="{{ route('worker.operation.collect.delete-operation-linen-product', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}" 
                       class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fa-solid fa-trash mr-2"></i> ลบ
                    </a>
                </div>
            </div>
        </x-worker.card>
        @endforeach
        
        @if(count($operationLinenProducts) == 0)
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <i class="fa-regular fa-folder-open text-4xl mb-4"></i>
                <p>ยังไม่มีรายการที่เลือก</p>
                <a href="{{ route('worker.operation.collect.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => 0]) }}" class="mt-4 inline-block text-blue-600 hover:underline">
                    เพิ่มรายการใหม่
                </a>
            </div>
        @endif
    </div>
</x-worker.page>
@endsection
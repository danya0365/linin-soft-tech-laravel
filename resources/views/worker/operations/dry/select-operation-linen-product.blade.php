@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="เลือกที่จะแก้ไขหรือลบ" 
    subtitle="Manage Operation Items" 
    icon="fa-solid fa-pen-to-square"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.dry.select-employee')],
        ['label' => 'ลูกค้า: ' . $operation['customer']['name'], 'route' => route('worker.operation.dry.select-customer', ['operationId' => $operation['id']])],
        ['label' => 'เครื่องอบผ้า: ' . $operation['dryer_machine']['name'], 'route' => route('worker.operation.dry.select-dryer-machine', ['operationId' => $operation['id']])],
        ['label' => 'สรุปข้อมูลการอบ', 'route' => route('worker.operation.dry.employee-summary', ['operationId' => $operation['id']])],
        ['label' => 'แก้ไขรายการ']
    ]"
>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($operationLinenProducts as $operationLinenProduct)
        <x-worker.card>
            <div class="space-y-4">
                {{-- Header / Title (Linen Case) --}}
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">เคสงาน</span>
                    <span class="font-medium text-gray-900 dark:text-white">
                        {{ $operationLinenProduct['linen_case'] ? $operationLinenProduct['linen_case']['name'] : 'ยังไม่ได้เลือก' }}
                    </span>
                </div>

                {{-- Details List --}}
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ชนิดผ้า</span>
                        <span class="font-medium text-gray-900 dark:text-gray-200">
                            {{ $operationLinenProduct['linen_product'] ? $operationLinenProduct['linen_product']['name'] : 'ยังไม่ได้เลือก' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        {{-- Fixed typo: wet_weight -> dry_weight and Label น้ำหนักเปียก -> น้ำหนักอบ --}}
                        <span class="text-gray-500">น้ำหนักอบ</span>
                        <span class="font-medium text-gray-900 dark:text-gray-200">
                            {{ $operationLinenProduct['dry_weight'] ? $operationLinenProduct['dry_weight'] . ' kg.' : 'ยังไม่ได้เลือก' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">สี</span>
                        <div class="flex items-center gap-2">
                            @if($operationLinenProduct['color'])
                                <div class="w-4 h-4 rounded-full border border-gray-200 shadow-sm" style="background-color: {{ $operationLinenProduct['color'] }}"></div>
                                <span class="font-medium text-gray-900 dark:text-gray-200">{{ $operationLinenProduct['color'] }}</span>
                            @else
                                <span class="text-gray-400">ยังไม่ได้เลือก</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="grid grid-cols-2 gap-3 pt-4 mt-auto">
                    <a href="{{ route('worker.operation.dry.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}" 
                       class="flex items-center justify-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-amber-700 bg-amber-100 hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                        <i class="fa fa-pen mr-2"></i> แก้ไข
                    </a>
                    
                    <a href="{{ route('worker.operation.dry.delete-operation-linen-product', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}" 
                       class="flex items-center justify-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i class="fa fa-trash mr-2"></i> ลบเลย
                    </a>
                </div>
            </div>
        </x-worker.card>
        @endforeach
    </div>
</x-worker.page>
@endsection
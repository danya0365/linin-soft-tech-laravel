@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="น้ำหนักและจำนวนที่จัดเก็บ" 
    subtitle="Collect Weight and Quantity" 
    icon="fa-solid fa-scale-balanced"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.collect.select-employee')],
        ['label' => 'ลูกค้า: ' . $operation['customer']['name'], 'route' => route('worker.operation.collect.select-customer', ['operationId' => $operation['id']])],
        ['label' => 'สรุปข้อมูลการจัดเก็บ', 'route' => route('worker.operation.collect.employee-summary', ['operationId' => $operation['id']])],
        ['label' => 'เคสงาน: ' . $operationLinenProduct['linen_case']['name'], 'route' => route('worker.operation.collect.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']])],
        ['label' => 'ประเภทผ้า: ' . $operationLinenProduct['linen_product']['name'], 'route' => route('worker.operation.collect.select-linen-product', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']])],
        ['label' => 'น้ำหนัก/จำนวน/สี']
    ]"
>
    <x-worker.card title="บันทึกข้อมูล">
        <form method="POST" action="{{ route('worker.operation.collect.set-select-weight-and-color', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}" role="form" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">น้ำหนัก (Kg)</h3>
                    <x-number-pad :inputName="'collect_weight'" :inputValue="$operationLinenProduct['collect_weight']">
                        น้ำหนักกิโลกรัม
                    </x-number-pad>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">จำนวนแพ็ค</h3>
                    <x-number-pad :inputName="'collect_pack'" :inputValue="$operationLinenProduct['collect_pack']">
                        จำนวนแพ็ค
                    </x-number-pad>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สี</h3>
                    <x-color-pad :inputName="'color'" :inputValue="$operationLinenProduct['color']">
                        สี
                    </x-color-pad>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    ยืนยัน
                </button>
                <button type="button" 
                        onclick="location.reload()"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    คืนค่า
                </button>
            </div>
        </form>
    </x-worker.card>
</x-worker.page>
@endsection
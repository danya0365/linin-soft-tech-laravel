@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="น้ำหนักเปียกและสี" 
    subtitle="Wet Weight and Color" 
    icon="fa-solid fa-scale-unbalanced"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.wash.select-employee')],
        ['label' => 'ลูกค้า: ' . $operation['customer']['name'], 'route' => route('worker.operation.wash.select-customer', ['operationId' => $operation['id']])],
        ['label' => 'เครื่องซักผ้า: ' . $operation['washing_machine']['name'], 'route' => route('worker.operation.wash.select-washing-machine', ['operationId' => $operation['id']])],
        ['label' => 'เคสงาน: ' . $operationLinenProduct['linen_case']['name'], 'route' => route('worker.operation.wash.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']])],
        ['label' => 'ประเภทผ้า: ' . $operationLinenProduct['linen_product']['name'], 'route' => route('worker.operation.wash.select-linen-product', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']])],
        ['label' => 'น้ำหนัก/สี']
    ]"
>
    <x-worker.card title="บันทึกข้อมูล">
        <form method="POST" action="{{ route('worker.operation.wash.set-select-weight-and-color', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}"  role="form" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">น้ำหนัก (กิโลกรัม)</h3>
                    <x-number-pad :inputName="'wet_weight'" :inputValue="$operationLinenProduct['wet_weight']">
                        น้ำหนักกิโลกรัม
                    </x-number-pad>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สี</h3>
                    <x-color-pad :inputName="'color'" :inputValue="$operationLinenProduct['color']">
                        สี
                    </x-color-pad>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="space-y-1">
                    <label for="operation_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">วันที่บันทึก - Create Date</label>
                    <input type="text" 
                           id="operation_date"
                           name="operation_date" 
                           value="{{ $operationLinenProduct['operation_date'] }}" 
                           placeholder="YYYY-MM-DD" 
                           pattern="(?:19|20)(?:[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:29|30))|(?:(?:0[13578]|1[02])-31))|(?:[13579][26]|[02468][048])-02-29)"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm h-12">
                    @error('operation_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="operation_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">เวลาบันทึก - Create Time</label>
                    <input type="text" 
                           id="operation_time"
                           name="operation_time" 
                           value="{{ $operationLinenProduct['operation_time'] }}" 
                           placeholder="HH:MM" 
                           pattern="([01]?[0-9]{1}|2[0-3]{1}):[0-5]{1}[0-9]{1}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm h-12">
                    @error('operation_time')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-8">
                <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                    ยืนยัน
                </button>
                <button type="button" 
                        onclick="location.reload()"
                        class="w-full flex justify-center py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                    คืนค่า
                </button>
            </div>
        </form>
    </x-worker.card>
</x-worker.page>
@endsection
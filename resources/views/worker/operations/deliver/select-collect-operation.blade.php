@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="เลือกรายการที่ต้องการส่ง" 
    subtitle="Select Items to Deliver" 
    icon="fa-solid fa-boxes-packing"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.deliver.select-employee')],
        ['label' => 'รถ: ' . ($operation['truck']['name'] ?? '-'), 'route' => route('worker.operation.deliver.select-truck', ['operationId' => $operation['id']])],
        ['label' => 'สรุปข้อมูลการขนส่ง', 'route' => route('worker.operation.deliver.employee-summary', ['operationId' => $operation['id']])],
        ['label' => 'เลือกรายการ']
    ]"
>
    <x-worker.card title="รายการทั้งหมดที่รอรการจัดส่ง">
        <form class="form" method="POST" action="{{ route('worker.operation.deliver.select-collect-operation', ['operationId' => $operation['id']]) }}" role="form" enctype="multipart/form-data">
            @csrf
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">วันที่/เวลา</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">สินค้า</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ลูกค้า</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">เก็บ (Pack)</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ส่ง (Pack)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($operations as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col">
                                        <span>{{ $item->created_at->format('Y-m-d') }}</span>
                                        <span class="text-xs">{{ $item->created_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $item->linenProduct ? $item->linenProduct->name : '-' }}
                                        </span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                {{ App\Enums\OperationType::getDescription($item->operation->operation_type) }}
                                            </span>
                                            @if($item->color)
                                                <span class="w-3 h-3 rounded-full border border-gray-200 dark:border-gray-600" style="background-color: {{ $item->color }}" title="{{ $item->color }}"></span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $item->operation->customer->name ?? "-" }}</span>
                                        <span class="text-xs mt-0.5">โดย: {{ $item->operation->employee->name ?? "-" }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                                    <div class="flex flex-col items-center">
                                        <span class="font-bold">{{ $item->collect_pack }}</span>
                                        <span class="text-xs text-gray-500">({{ $item->collect_weight }} kg)</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input 
                                        data-id="{{ $item->id }}" 
                                        type="number" 
                                        class="form-control input-value w-24 mx-auto text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" 
                                        name="operationLinenProducts[{{ $item->id }}]" 
                                        autocomplete="off"
                                        placeholder="0"
                                    >
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-4 flex gap-4">
                <button type="submit" class="flex-1 flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    ยืนยันที่เลือก
                </button>
                <button type="button" class="flex-1 flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    ยกเลิก
                </button>
            </div>
        </form>
        
        <div class="mt-4">
            {!! $operations->withQueryString()->links() !!}
        </div>
    </x-worker.card>
</x-worker.page>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') return;
    
    var operations = @json($operations);
    $(function(){

        function validate(e){
            var selectCheckbox = [];
            $('.input-value').each(function() {
                if ($(this).val() != "" && $(this).val() > 0) {
                    selectCheckbox.push({'id': $(this).data('id'), 'value': $(this).val()})
                }
            });

            // Note: The original validation logic mapped IDs to check names but didn't use it for the alert.
            // It just checked if any checkbox (input) had value.
    
            if (!selectCheckbox || selectCheckbox.length === 0) {
                Swal.fire('กรุณาระบุจำนวนอย่างน้อย 1 รายการ', '', 'error')
                return false
            }
            return true;
        }
    
        $('.form').on('submit', function(e){
            return validate(e);
        })
    })
    
});
</script>
@endpush
@endsection
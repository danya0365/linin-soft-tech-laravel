@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="สรุปข้อมูลการจัดเก็บ" 
    subtitle="Collect Summary" 
    icon="fa-solid fa-clipboard-list"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.collect.select-employee')],
        ['label' => 'ลูกค้า: ' . $operation['customer']['name'], 'route' => route('worker.operation.collect.select-customer', ['operationId' => $operation['id']])],
        ['label' => 'สรุปข้อมูล']
    ]"
>
    <div class="space-y-6">
        <!-- Status and Actions -->
        <x-worker.card title="สถานะการจัดเก็บ">
            <x-slot:action>
                 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $operation['status'] == 'close' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                    {{ ucfirst($operation['status']) }}
                </span>
            </x-slot:action>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <button type="button" id="add-operation"
                        class="flex flex-col items-center justify-center p-4 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200">
                    <div class="mb-2"><i class="fa-solid fa-plus text-2xl"></i></div>
                    <span class="font-medium">เพิ่มรายการ</span>
                </button>

                <button type="button" id="edit-operation"
                        class="flex flex-col items-center justify-center p-4 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 text-gray-600 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 transition-all duration-200">
                    <div class="mb-2"><i class="fa-solid fa-pen-to-square text-2xl"></i></div>
                    <span class="font-medium">แก้ไข/ลบ</span>
                </button>

                @if ($operation['status'] != "close")
                <button type="button" id="close-operation"
                        class="flex flex-col items-center justify-center p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:bg-green-50 dark:hover:bg-green-900/20 hover:border-green-500 text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-all duration-200">
                    <div class="mb-2"><i class="fa-solid fa-check-circle text-2xl"></i></div>
                    <span class="font-medium">ปิดงาน</span>
                </button>
                @else
                <button type="button" id="reopen-operation"
                        class="flex flex-col items-center justify-center p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-500 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200">
                    <div class="mb-2"><i class="fa-solid fa-arrow-rotate-left text-2xl"></i></div>
                    <span class="font-medium">เปิดใหม่</span>
                </button>
                @endif
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">น้ำหนักรวม</span>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $operation['total_collect_weight'] ? number_format($operation['total_collect_weight'], 2) : '0' }}
                            <span class="text-sm font-normal text-gray-500">kg</span>
                        </span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">จำนวนแพ็ครวม</span>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $operation['total_collect_pack'] ? number_format($operation['total_collect_pack']) : '0' }}
                            <span class="text-sm font-normal text-gray-500">packs</span>
                        </span>
                    </div>
                </div>

                <!-- Mini List of items -->
                <div class="space-y-3">
                    @foreach ($operationLinenProducts as $operationLinenProduct)
                    <div class="flex items-start justify-between p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg text-sm">
                        <div class="space-y-1">
                            <div class="font-medium text-gray-900 dark:text-white">
                                {{ $operationLinenProduct['linen_product'] ? $operationLinenProduct['linen_product']['name'] : 'ยังไม่ได้เลือก' }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $operationLinenProduct['linen_case'] ? $operationLinenProduct['linen_case']['name'] : '-' }}
                            </div>
                        </div>
                        <div class="text-right space-y-1">
                            <div class="font-medium text-gray-900 dark:text-white">
                                {{ $operationLinenProduct['collect_weight'] ? $operationLinenProduct['collect_weight'] : '0' }} kg
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $operationLinenProduct['collect_pack'] ? $operationLinenProduct['collect_pack'] : '0' }} packs
                            </div>
                            @if($operationLinenProduct['color'])
                            <div style="color: {{ $operationLinenProduct['color'] }}" class="text-xs font-medium">
                                {{ $operationLinenProduct['color'] }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-4 text-center text-xs text-gray-400">
                    <i class="fa-regular fa-clock mr-1"></i>
                    เวลาในการจัดเก็บผ้า: {{ $operationTimeDuration }}
                </div>
            </div>
        </x-worker.card>

        <!-- Employee Summary -->
        <x-worker.card title="สรุปข้อมูลพนักงาน">
            <div class="flex flex-col items-center mb-6">
                <x-employee-avatar :photo="$operation['collect_employee']['photo']" class="w-24 h-24 rounded-full mb-3 shadow-md" />
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $operation['collect_employee']['name'] }}</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $operation['collect_employee']['code'] }}</span>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                @foreach ($summaryReports as $summaryReport)
                <div class="bg-gray-50 dark:bg-gray-800/50 p-3 rounded-lg flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $summaryReport['title'] }}</dt>
                    <dd class="text-sm font-bold text-gray-900 dark:text-white">
                        {{ number_format($summaryReport['value']) }} <span class="text-xs font-normal text-gray-500">{{ $summaryReport['unit'] ?? '' }}</span>
                    </dd>
                </div>
                @endforeach
            </dl>
            
            <div class="mt-4 text-center text-xs text-gray-400">
                <i class="fa-regular fa-clock mr-1"></i>
                เวลาการทำงานทั้งหมด: {{ $workingDuration }}
            </div>
        </x-worker.card>
    </div>
</x-worker.page>

@push('scripts')
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') return;
    $(function(){

        var operationStatus = '{{ $operation['status'] }}';

        function askBeforeExit(e) {
            if(!e) e = window.event;
            e.cancelBubble = true;
            e.returnValue = 'You sure you want to leave?'; 
    
            if (e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            }
        }

        function removeOnUnload(callback){
            window.onbeforeunload = null;
            window.pagehide = null;
            callback()
        }

        function setUpOnUnload(){
            window.onbeforeunload = operationStatus == 'close' ? null : askBeforeExit;
            window.pagehide = operationStatus == 'close' ? null : askBeforeExit;
        }

        function closeOperation(){
            removeOnUnload(function(){
                window.location='{{ route('worker.operation.collect.set-close', ['operationId' => $operation['id']]) }}'
            })
        }

        function reopenOperation(){
            removeOnUnload(function(){
                window.location='{{ route('worker.operation.collect.set-in-progress', ['operationId' => $operation['id']]) }}'
            })
        }

        function addOperation(){
            removeOnUnload(function(){
                window.location='{{ route('worker.operation.collect.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => 0]) }}'
            })
        }

        function editOperation(){
            removeOnUnload(function(){
                window.location='{{ route('worker.operation.collect.select-operation-linen-product', ['operationId' => $operation['id']]) }}'
            })
        }

        setUpOnUnload();
        $("#close-operation").on("click", closeOperation);
        $("#reopen-operation").on("click", reopenOperation);
        $("#add-operation").on("click", addOperation);
        $("#edit-operation").on("click", editOperation);
    })
});
</script>
@endpush
@endsection
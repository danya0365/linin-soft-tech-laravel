@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="สรุปข้อมูลการรีด" 
    subtitle="Iron Operation Summary" 
    icon="fa-solid fa-clipboard-check"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.iron.select-employee')],
        ['label' => 'ลูกค้า: ' . $operation['customer']['name'], 'route' => route('worker.operation.iron.select-customer', ['operationId' => $operation['id']])],
        ['label' => 'สรุปข้อมูล']
    ]"
>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Left Column: Status & Actions --}}
        <x-worker.card title="สถานะการรีด">
            <div class="space-y-6">
                {{-- Action Buttons --}}
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" 
                            onclick="window.location='{{ route('worker.operation.iron.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => 0]) }}'"
                            class="flex flex-col items-center justify-center p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors border border-blue-200 dark:border-blue-800">
                        <i class="fa fa-plus mb-1 text-lg"></i>
                        <span class="text-xs font-semibold">เพิ่ม</span>
                    </button>

                    <button type="button" 
                            onclick="window.location='{{ route('worker.operation.iron.select-operation-linen-product', ['operationId' => $operation['id']]) }}'"
                            class="flex flex-col items-center justify-center p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-colors border border-amber-200 dark:border-amber-800">
                        <i class="fa fa-pen-to-square mb-1 text-lg"></i>
                        <span class="text-xs font-semibold text-center">แก้ไข/ลบ</span>
                    </button>

                    @if ( $operation['status'] != "close")
                    <button type="button" 
                            id="close-operation"
                            class="flex flex-col items-center justify-center p-3 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors border border-green-200 dark:border-green-800">
                        <i class="fa fa-check-circle mb-1 text-lg"></i>
                        <span class="text-xs font-semibold">ปิดงาน</span>
                    </button>
                    @else 
                    <button type="button" 
                            id="reopen-operation"
                            class="flex flex-col items-center justify-center p-3 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-600">
                        <i class="fa fa-rotate-left mb-1 text-lg"></i>
                        <span class="text-xs font-semibold">เปิดใหม่</span>
                    </button>
                    @endif
                </div>

                {{-- Status Details --}}
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-3">
                    <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-2">
                        <span class="text-gray-500 text-sm">สถานะ</span>
                        <span class="font-semibold {{ $operation['status'] == 'close' ? 'text-green-600' : 'text-amber-600' }} uppercase">
                            {{ $operation['status'] }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-2">
                        <span class="text-gray-500 text-sm">จำนวนที่รีดทั้งหมด</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $operation['total_iron_piece'] ? $operation['total_iron_piece'] : '-' }} ชิ้น</span>
                    </div>
                    <div class="flex justify-between items-center pb-2">
                        <span class="text-gray-500 text-sm">เวลาในการรีดผ้า</span>
                        <span class="font-mono text-gray-900 dark:text-white">{{ $operationTimeDuration }}</span>
                    </div>
                </div>

                {{-- Items List --}}
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">รายการที่เลือก</h4>
                    <div class="space-y-2">
                        @forelse ($operationLinenProducts as $operationLinenProduct)
                        <div class="flex items-start p-3 bg-white dark:bg-gray-700 rounded-md border border-gray-100 dark:border-gray-600 shadow-sm">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $operationLinenProduct['linen_case'] ? $operationLinenProduct['linen_case']['name'] : 'ยังไม่ได้เลือก' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $operationLinenProduct['linen_product'] ? $operationLinenProduct['linen_product']['name'] : 'ยังไม่ได้เลือก' }}
                                </p>
                            </div>
                            <div class="text-right ml-4">
                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $operationLinenProduct['iron_piece'] ? $operationLinenProduct['iron_piece'] : '-' }} ชิ้น
                                </span>
                                @if($operationLinenProduct['color'])
                                <span class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span class="w-2 h-2 rounded-full mr-1" style="background-color: {{ $operationLinenProduct['color'] }}"></span>
                                    {{ $operationLinenProduct['color'] }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500 text-center py-4 italic">ยังไม่มีรายการ</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </x-worker.card>


        {{-- Right Column: Employee Summary --}}
        <x-worker.card title="สรุปข้อมูลการรีดของพนักงาน">
            <div class="flex flex-col items-center py-6 border-b border-gray-100 dark:border-gray-700">
                <div class="w-24 h-24 mb-4">
                    <x-employee-avatar :photo="$operation['iron_employee']['photo']" class="w-full h-full rounded-full shadow-md" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $operation['iron_employee']['name'] }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $operation['iron_employee']['code'] }}</p>
            </div>

            <div class="py-4 space-y-3">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">รายงานสรุป</h4>
                @foreach ($summaryReports as $summaryReport)
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $summaryReport['title'] }}</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($summaryReport['value']) }} ชิ้น</span>
                </div>
                @endforeach
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    เวลาการทำงานทั้งหมด: <span class="font-mono font-medium text-gray-900 dark:text-white">{{ $workingDuration }}</span>
                </p>
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
            //e.cancelBubble is supported by IE - this will kill the bubbling process.
            e.cancelBubble = true;
            e.returnValue = 'You sure you want to leave?'; //This is displayed on the dialog
    
            //e.stopPropagation works in Firefox.
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
                window.location='{{ route('worker.operation.iron.set-close', ['operationId' => $operation['id']]) }}'
            })
        }

        function reopenOperation(){
            removeOnUnload(function(){
                window.location='{{ route('worker.operation.iron.set-in-progress', ['operationId' => $operation['id']]) }}'
            })
        }

        setUpOnUnload();
        $("#close-operation").on("click", closeOperation);
        $("#reopen-operation").on("click", reopenOperation);
        })
});
</script>
@endpush

@endsection
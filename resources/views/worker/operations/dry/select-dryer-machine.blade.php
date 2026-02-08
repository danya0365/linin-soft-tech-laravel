@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="เลือกเครื่องอบผ้า" 
    subtitle="Select Dryer Machine" 
    icon="fa-solid fa-wind"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.dry.select-employee')],
        ['label' => 'ลูกค้า: ' . $operation['customer']['name'], 'route' => route('worker.operation.dry.select-customer', ['operationId' => $operation['id']])],
        ['label' => 'อบ - เลือกเครื่องอบผ้า']
    ]"
>
    {{-- Error Alert --}}
    @if (session('error'))
    <div class="mb-6 rounded-lg bg-red-50 dark:bg-red-900/30 p-4 border border-red-200 dark:border-red-800">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fa fa-exclamation-circle text-red-500 dark:text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                    เกิดข้อผิดพลาด
                </h3>
                <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                    <a href="{{ route('worker.operation.dry.employee-summary', ['operationId' => session('operation_id')]) }}" class="underline hover:text-red-900 dark:hover:text-red-100">
                        {{ session('error') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <x-worker.card title="เครื่องอบผ้า">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            @foreach ($dryerMachines as $dryerMachine)
            @php
                $isSelected = $operation['dryer_machine_id'] == $dryerMachine['id'];
                $statusClass = $isSelected ? 'text-green-600 dark:text-green-400 font-bold' : 'text-gray-500 dark:text-gray-400';
                $borderClass = $isSelected ? 'border-green-500 ring-2 ring-green-200 dark:ring-green-900 bg-green-50 dark:bg-green-900/20' : 'border-transparent hover:border-amber-500 hover:bg-white dark:hover:bg-gray-700';
            @endphp
            <a href="{{ route('worker.operation.dry.set-select-dryer-machine', ['operationId' => $operation['id'], 'dryerMachineId' => $dryerMachine['id']]) }}" 
               class="group relative flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 {{ $borderClass }} shadow-sm hover:shadow-md transition-all duration-200"
               style="min-height: 150px">
                
                <div class="mb-3 transform group-hover:scale-110 transition-transform duration-200">
                    <div class="w-16 h-16 flex items-center justify-center rounded-full transition-colors {{ $isSelected ? 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/30 group-hover:text-amber-600 dark:group-hover:text-amber-400' }}">
                        <i class="bi {{ $dryerMachine['photo'] }} text-3xl"></i>
                    </div>
                </div>
                
                <div class="text-center w-full">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                        {{ $dryerMachine['name'] }}
                    </h4>
                    <div class="text-xs {{ $statusClass }}">
                        @if($isSelected)
                            <span><i class="fa fa-check-circle mr-1"></i> เลือกอยู่</span>
                        @else
                            {!! $dryerMachine['status_text'] !!}
                        @endif
                    </div>
                </div>

                @if($isSelected)
                <div class="absolute top-2 right-2">
                    <i class="fa fa-check-circle text-green-500 text-lg"></i>
                </div>
                @endif
            </a>
            @endforeach
        </div>
    </x-worker.card>
</x-worker.page>
@endsection
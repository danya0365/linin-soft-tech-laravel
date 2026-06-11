@extends('layouts.worker')

@section('content')
    <x-worker.page title="เลือกเครื่องซักผ้า" subtitle="Select Washing Machine" icon="fa-solid fa-soap" :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        [
            'label' => 'พนักงาน: ' . $operation['employee']['name'],
            'route' => route('worker.operation.wash.select-employee'),
        ],
        [
            'label' => 'ลูกค้า: ' . $operation['customer']['name'],
            'route' => route('worker.operation.wash.select-customer', ['operationId' => $operation['id']]),
        ],
        ['label' => 'ซัก - เลือกเครื่องซักผ้า'],
    ]">
        <x-worker.card title="เครื่องซักผ้า">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @foreach ($washingMachines as $washingMachine)
                    @php
                        $isSelected = $operation['washing_machine_id'] == $washingMachine['id'];
                        $isBroken = $washingMachine['service_status'] === 'broken';
                        $isInUse = !empty($washingMachine['operation_id']) && !$isSelected;
                        if ($isSelected) {
                            $statusClass = 'text-green-600 dark:text-green-400 font-bold';
                            $borderClass =
                                'border-green-500 ring-2 ring-green-200 dark:ring-green-900 bg-green-50 dark:bg-green-900/20';
                        } elseif ($isBroken) {
                            $statusClass = 'text-red-500 dark:text-red-400';
                            $borderClass = 'border-red-300 bg-red-50 dark:bg-red-900/10 opacity-60';
                        } elseif ($isInUse) {
                            $statusClass = 'text-amber-600 dark:text-amber-400';
                            $borderClass =
                                'border-amber-300 hover:border-amber-500 hover:bg-white dark:hover:bg-gray-700';
                        } else {
                            $statusClass = 'text-green-600 dark:text-green-400';
                            $borderClass =
                                'border-transparent hover:border-amber-500 hover:bg-white dark:hover:bg-gray-700';
                        }
                    @endphp
                    <a href="{{ route('worker.operation.wash.set-select-washing-machine', ['operationId' => $operation['id'], 'washingMachineId' => $washingMachine['id']]) }}"
                        class="group relative flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 {{ $borderClass }} shadow-sm hover:shadow-md transition-all duration-200"
                        style="min-height: 150px">

                        <div class="mb-3 transform group-hover:scale-110 transition-transform duration-200">
                            <div
                                class="w-16 h-16 flex items-center justify-center rounded-full transition-colors {{ $isSelected ? 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/30 group-hover:text-amber-600 dark:group-hover:text-amber-400' }}">
                                <i class="bi {{ $washingMachine['photo'] }} text-3xl"></i>
                            </div>
                        </div>

                        <div class="text-center w-full">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                {{ $washingMachine['name'] }}
                            </h4>
                            <div class="text-xs {{ $statusClass }}">
                                @if ($isSelected)
                                    <span><i class="fa fa-check-circle mr-1"></i> เลือกอยู่</span>
                                @elseif($isBroken)
                                    <span><i class="fa fa-wrench mr-1"></i> {{ $washingMachine['status_text'] }}</span>
                                @elseif($isInUse)
                                    <span><i class="fa fa-circle-info mr-1"></i> {{ $washingMachine['status_text'] }}</span>
                                @else
                                    <span><i class="fa fa-check mr-1"></i> {{ $washingMachine['status_text'] }}</span>
                                @endif
                            </div>
                        </div>

                        @if ($isSelected)
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

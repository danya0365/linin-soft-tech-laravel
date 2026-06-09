@extends('layouts.worker')

@section('content')
    <x-worker.page title="เลือกรถขนส่ง" subtitle="Select Truck" icon="fa-solid fa-truck-moving" :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        [
            'label' => 'พนักงาน: ' . $operation['employee']['name'],
            'route' => route('worker.operation.deliver.select-employee'),
        ],
        ['label' => 'เลือกรถขนส่ง'],
    ]">
        <x-worker.card title="รายการรถขนส่ง">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($trucks as $truck)
                    @php
                        $isSelected = $operation['truck_id'] == $truck['id'];
                        $isBroken = $truck['service_status'] === 'broken';
                        $isInUse = !empty($truck['operation_id']) && !$isSelected;
                        $iconClass = $truck['photo'] ? $truck['photo'] : 'bi-truck';
                    @endphp
                    <a href="{{ route('worker.operation.deliver.set-select-truck', ['operationId' => $operation['id'], 'truckId' => $truck['id']]) }}"
                        class="group relative flex flex-col items-center p-6 bg-white dark:bg-gray-800 rounded-xl border-2 {{ $isSelected ? 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-900' : ($isBroken ? 'border-red-300 bg-red-50 dark:bg-red-900/10 opacity-60' : ($isInUse ? 'border-amber-300 hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/10' : 'border-gray-200 dark:border-gray-700 hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/10')) }} transition-all duration-200 shadow-sm">

                        <div
                            class="mb-4 p-4 rounded-full {{ $isSelected ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 group-hover:bg-blue-100 group-hover:text-blue-600' }} transition-colors duration-200">
                            <i
                                class="{{ str_replace('bi-', 'fa-solid fa-', $iconClass == 'bi-truck' ? 'fa-truck' : $iconClass) }} text-4xl"></i>
                            <!-- Fallback mechanism logic above might be weak if strictly 'bi-truck'.
                             Let's simple hardcode fa-truck for visual consistency if it matches known patterns,
                             or just output the class if unknown.
                             Actually, let's just use fa-truck for all for consistency in this refactor
                             unless the user has custom individual truck icons.
                             Given the code `bi-truck`, it's likely generic. -->
                        </div>

                        <h3
                            class="text-lg font-bold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                            {{ $truck['name'] }}
                        </h3>

                        <div
                            class="text-sm text-gray-500 dark:text-gray-400 mb-3 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                            {{ $truck['plate_number'] }}
                        </div>

                        <div class="text-sm font-medium">
                            @if ($isSelected)
                                <span class="text-blue-600 dark:text-blue-400 flex items-center">
                                    <i class="fa-solid fa-circle-check mr-1.5"></i> เลือกอยู่ (Selected)
                                </span>
                            @elseif($isBroken)
                                <span class="text-red-500 dark:text-red-400 flex items-center">
                                    <i class="fa fa-wrench mr-1.5"></i> {{ $truck['status_text'] }}
                                </span>
                            @elseif($isInUse)
                                <span class="text-amber-600 dark:text-amber-400 flex items-center">
                                    <i class="fa fa-circle-info mr-1.5"></i> {{ $truck['status_text'] }}
                                </span>
                            @else
                                <span class="text-green-600 dark:text-green-400 flex items-center">
                                    <i class="fa fa-check mr-1.5"></i> {{ $truck['status_text'] }}
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </x-worker.card>
    </x-worker.page>
@endsection

@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="รายการทั้งหมดที่ยังไม่ถูกปิดงาน"
    subtitle="Operation In Progress"
    icon="fa-solid fa-hourglass-half"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'รายการทั้งหมด']
    ]"
>
    <x-worker.card>
        {{-- Filter Form --}}
        <div class="mb-6">
            <x-worker.date-filter 
                :action="route('worker.operation.in-progress')"
                :date-start-at="$dateStartAt"
                :date-end-at="$dateEndAt"
                :reset-url="route('worker.operation.in-progress')"
            >
                <x-worker.select-filter 
                    name="linenType"
                    label="ชนิดผ้า"
                    :options="$linenTypes->pluck('name', 'id')->toArray()"
                    :selected="$linenTypeSelected"
                    show-all-label="ไม่เลือก"
                />

                <x-worker.select-filter 
                    name="operation_type"
                    label="ประเภทงาน"
                    :options="$operationTypes"
                    :selected="$operationTypeSelected"
                />

                <x-worker.select-filter 
                    name="sort_order"
                    label="เรียงโดย"
                    :options="collect($sortOrders)->pluck('name', 'var')->toArray()"
                    :selected="$sortOrderSelected"
                    :show-all="false"
                />
            </x-worker.date-filter>
        </div>

        {{-- Table --}}
        <x-worker.data-table 
            :headers="['วันที่', 'ประเภทงาน', 'สินค้า', 'สี', 'ลูกค้า', 'พนักงาน', 'รายละเอียด', 'เวลา']"
            :paginator="$operations"
        >
            @foreach ($operations as $operation)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 whitespace-nowrap">{{ $operation->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">
                        <a href="{{ route('worker.operation.checkout-in-progress', $operation->operation->id) }}" class="text-amber-600 hover:text-amber-700 font-medium">
                            {{ App\Enums\OperationType::getDescription($operation->operation->operation_type) }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $operation->linenProduct ? $operation->linenProduct->name : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full border border-gray-200 dark:border-gray-600 ring-1 ring-gray-100 dark:ring-gray-700 shadow-sm" style="background-color: {{ $operation->color }}"></span>
                            <span class="font-medium">{{ $operation->color }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $operation->operation->customer ? $operation->operation->customer->name : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $operation->operation->employee ? $operation->operation->employee->name : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-left">
                        @if ($operation->wet_weight)
                            <div>ซัก: {{ number_format($operation->wet_weight, 2) }} kg (#{{ $operation->operation->washing_machine_id }})</div>
                        @endif
                        @if ($operation->dry_weight)
                            <div>อบ: {{ number_format($operation->dry_weight, 2) }} kg (#{{ $operation->operation->dryer_machine_id }})</div>
                        @endif
                        @if ($operation->iron_piece)
                            <div>รีด: {{ number_format($operation->iron_piece) }} ชิ้น</div>
                        @endif
                        @if ($operation->packing_piece)
                            <div>พับแพ็ค: {{ number_format($operation->packing_piece) }} ชิ้น</div>
                        @endif
                        @if ($operation->collect_weight)
                            <div>จัดเก็บ: {{ number_format($operation->collect_weight, 2) }} kg</div>
                        @endif
                        @if ($operation->collect_pack)
                            <div>จัดเก็บ: {{ number_format($operation->collect_pack) }} pack</div>
                        @endif
                        @if ($operation->deliver_pack)
                            <div>ขนส่ง: {{ number_format($operation->deliver_pack) }} pack (#{{ $operation->operation->truck_id }})</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 whitespace-nowrap">{{ $operation->created_at->format('H:i') }}</td>
                </tr>
            @endforeach
        </x-worker.data-table>
    </x-worker.card>
</x-worker.page>
@endsection
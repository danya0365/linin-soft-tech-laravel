@extends('layouts.worker')

@section('content')
<x-menu.page-layout 
    title="{{ $linenCase['name'] }}"
    subtitle="รายการทั้งหมด"
    icon="bi {{ $linenCase['icon'] ?? 'bi-list' }}"
    icon-color="text-blue-600"
    :breadcrumbs="[
        ['label' => 'Worker', 'url' => route('worker')],
        ['label' => 'สินค้า', 'url' => route('worker.product')],
        ['label' => 'เคสงาน', 'url' => route('worker.product.select-linen-case')],
        ['label' => $linenCase['name']]
    ]"
>
    {{-- Filter Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <x-worker.date-filter 
            :action="route('worker.product.get-operations-by-linen-case', ['linenCase' => $linenCase['var']])"
            :date-start-at="$dateStartAt"
            :date-end-at="$dateEndAt"
            :reset-url="route('worker.product.get-operations-by-linen-case', ['linenCase' => $linenCase['var']])"
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

    {{-- Operations Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        <div class="px-4 sm:px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">รายละเอียดการทำงาน</h3>
        </div>
        <div class="p-4 sm:p-6">
            <x-worker.data-table 
                :headers="['วันที่', 'ประเภทงาน', 'สินค้า', 'สี', 'ลูกค้า', 'พนักงาน', 'ซัก (kg.)', 'อบ (kg.)', 'รีด', 'พับแพ็ค', 'จัดเก็บ (kg.)', 'จัดเก็บ (pack)', 'ขนส่ง (pack)', 'เวลา']"
                :paginator="$operations"
            >
                @foreach ($operations as $operation)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $operation->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ App\Enums\OperationType::getDescription($operation->operation->operation_type) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $operation->linenProduct ? $operation->linenProduct->name : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200" style="background-color: {{ $operation->color }}">{{ $operation->color }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $operation->operation->customer->name ?? "-" }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $operation->operation->employee->name ?? "-" }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $operation->wet_weight }} (#{{ $operation->operation->washing_machine_id }})</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $operation->dry_weight }} (#{{ $operation->operation->dryer_machine_id }})</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $operation->iron_piece }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $operation->packing_piece }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $operation->collect_weight }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $operation->collect_pack }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $operation->deliver_pack }} (#{{ $operation->operation->truck_id }})</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $operation->created_at->format('H:i') }}</td>
                </tr>
                @endforeach
            </x-worker.data-table>
        </div>
    </div>

    {{-- Summary Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">สรุปตามสินค้า</h3>
        </div>
        <div class="p-4 sm:p-6">
            <x-worker.data-table 
                :headers="['สินค้า', 'ซัก (kg.)', 'อบ (kg.)', 'รีด', 'พับแพ็ค', 'จัดเก็บ (kg.)', 'จัดเก็บ (pack)', 'ขนส่ง (pack)']"
            >
                @foreach ($linenProductSummaries as $linenProductSummary)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 font-medium">{{ $linenProductSummary->linenProduct ? $linenProductSummary->linenProduct->name : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $linenProductSummary->total_wet_weight }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $linenProductSummary->total_dry_weight }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $linenProductSummary->total_iron_piece }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $linenProductSummary->total_packing_piece }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $linenProductSummary->total_collect_weight }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $linenProductSummary->total_collect_pack }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $linenProductSummary->total_deliver_pack }}</td>
                </tr>
                @endforeach
            </x-worker.data-table>
        </div>
    </div>
</x-menu.page-layout>
@endsection
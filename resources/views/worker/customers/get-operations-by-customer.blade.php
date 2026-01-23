@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="{{ $customer['name'] }}"
    subtitle="รายการทั้งหมด"
    icon="fa-hospital"
    :breadcrumbs="[
        ['label' => 'ลูกค้า', 'route' => route('worker.customer')],
        ['label' => 'ยอดรวม', 'route' => route('worker.customer.operation-summary')]
    ]"
>

    {{-- Filter Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <x-worker.date-filter 
            :action="route('worker.customer.get-operations-by-customer', ['customerId' => $customer['id']])"
            :date-start-at="$dateStartAt"
            :date-end-at="$dateEndAt"
            :reset-url="route('worker.customer.get-operations-by-customer', ['customerId' => $customer['id']])"
        >
            <x-worker.select-filter 
                name="operation_type"
                label="ประเภทงาน"
                :options="$operationTypes"
                :selected="$operationTypeSelected"
            />
            
            {{-- Custom optgroup select for linen products --}}
            <div class="flex items-center gap-0">
                <label for="linen_product_id" class="px-3 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 whitespace-nowrap">
                    สินค้า
                </label>
                <select id="linen_product_id" name="linen_product_id" onchange="this.form.submit()" class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-r-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[150px]">
                    <option value="">แสดงทั้งหมด - Show All</option>
                    @foreach ($linenTypes as $linenType)
                    <optgroup label="{{ $linenType['name'] }}">
                        @foreach ($linenType['linen_products'] as $linenProduct)
                        <option value="{{ $linenProduct['id'] }}" {{ $linenProductSelected == $linenProduct['id'] ? 'selected' : '' }}>{{ $linenProduct['name'] }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>
            
            <x-worker.select-filter 
                name="linen_case"
                label="ชนิด"
                :options="collect($linenCases)->pluck('name', 'var')->toArray()"
                :selected="$linenCaseSelected"
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

    {{-- Data Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">รายละเอียดการทำงาน</h3>
        </div>
        <div class="p-4 sm:p-6">
            <x-worker.data-table 
                :headers="['วันที่', 'ประเภทงาน', 'สินค้า', 'ชนิด', 'สี', 'พนักงาน', 'ซัก (kg.)', 'อบ (kg.)', 'รีด', 'พับแพ็ค', 'จัดเก็บ (kg.)', 'จัดเก็บ (pack)', 'ขนส่ง (pack)', 'เวลา']"
                :paginator="$operations"
            >
                @foreach ($operations as $operation)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $operation->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ App\Enums\OperationType::getDescription($operation->operation->operation_type) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $operation->linenProduct ? $operation->linenProduct->name : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">
                        <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700">{{ $operation->linen_case }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200" style="background-color: {{ $operation->color }}">{{ $operation->color }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $operation->operation->employee->name ?? "" }}</td>
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
</x-worker.page>
@endsection
@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="{{ __('รายการยอดรวม') }}"
    subtitle="สรุปยอดแยกตามลูกค้า"
    icon="fa-list"
    :breadcrumbs="[
        ['label' => 'ลูกค้า', 'route' => route('worker.customer')],
        ['label' => 'รายการยอดรวม']
    ]"
>
    {{-- Filter Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <x-worker.date-filter 
            :action="route('worker.customer.get-operations-group-by-customer')"
            :date-start-at="$dateStartAt"
            :date-end-at="$dateEndAt"
            :reset-url="route('worker.customer.get-operations-group-by-customer')"
        >
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
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">รายการยอดรวมแต่ละลูกค้า</h3>
        </div>
        <div class="p-4 sm:p-6">
            <x-worker.data-table 
                :headers="['ลูกค้า', 'น้ำหนักผ้าเปียก', 'น้ำหนักผ้าสะอาด', 'ผ้าแก้ไข (ระบบ)', '% ของเสีย', 'น้ำหนักลูกค้า', 'มากกว่า/น้อยกว่า']"
                :paginator="$operations"
            >
                @foreach ($operations as $operation)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">
                        @if ($operation->operationCustomer)
                        <a href="{{ route('worker.customer.get-operations-by-customer', ['customerId' => $operation->operationCustomer->id]) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                            {{ $operation->operationCustomer->name }}
                        </a>
                        @else
                        <i class="text-gray-400">ลูกค้าถูกลบ</i>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ number_format($operation->total_wet_weight) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ number_format($operation->total_collect_weight) }}</td>
                    <td class="px-4 py-3 text-sm text-green-600 dark:text-green-400 text-center font-medium">{{ number_format($operation->total_edit_collect_weight) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700">
                            {{ number_format($operation->total_collect_weight > 0 ? $operation->total_edit_collect_weight*100/$operation->total_collect_weight : 0) }}%
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ number_format($operation->total_billing_weight) }}</td>
                    <td class="px-4 py-3 text-sm text-center">
                        @php $diff = $operation->total_billing_weight - $operation->total_collect_weight; @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $diff >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ number_format($diff) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </x-worker.data-table>
        </div>
    </div>
</x-worker.page>
@endsection
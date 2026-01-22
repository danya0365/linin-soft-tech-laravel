@extends('layouts.worker')

@section('content')
<x-menu.page-layout 
    title="{{ __('รายการยอดรวม') }}"
    subtitle="สรุปยอดการซักแต่ละลูกค้า"
    icon="fa-chart-bar"
    icon-color="text-amber-600"
    :breadcrumbs="[
        ['label' => 'Worker', 'url' => route('worker')],
        ['label' => 'ลูกค้า', 'url' => route('worker.customer')],
        ['label' => 'รายการยอดรวม']
    ]"
>
    {{-- Filter Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <x-worker.date-filter 
            :action="route('worker.customer.operation-summary')"
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
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">ลูกค้า</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">น้ำหนักผ้าเปียก</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">น้ำหนักผ้าสะอาด</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" title="คำนวณอัตโนมัติจาก linen_case='edit'">
                                ผ้าแก้ไข (ระบบ) <i class="fa fa-info-circle text-green-500"></i>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase" title="กรอกโดย Supervisor ตอนออกบิล">
                                ผ้าแก้ไข (บันทึกมือ) <i class="fa fa-info-circle text-yellow-500"></i>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">% ของเสีย</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">น้ำหนักลูกค้า</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">มากกว่า/น้อยกว่า</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($operations as $operation)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">
                                @if ($operation->customer)
                                <a href="{{ route('worker.customer.get-operations-by-customer', ['customerId' => $operation->customer->id]) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $operation->customer->name }}
                                </a>
                                @else
                                <i class="text-gray-400">ลูกค้าถูกลบ</i>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ number_format($operation->total_wet_weight) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ number_format($operation->total_collect_weight) }}</td>
                            <td class="px-4 py-3 text-sm text-green-600 dark:text-green-400 text-center font-medium">{{ number_format($operation->total_edit_collect_weight) }}</td>
                            <td class="px-4 py-3 text-sm text-yellow-600 dark:text-yellow-400 text-center font-medium">{{ number_format($operation->total_edit_weight ?? 0) }}</td>
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
                    </tbody>
                    <tfoot class="bg-gray-100 dark:bg-gray-700">
                        <tr class="font-semibold">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">ยอดรวม</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ number_format($summary->total_wet_weight) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ number_format($summary->total_collect_weight) }}</td>
                            <td class="px-4 py-3 text-sm text-green-600 dark:text-green-400 text-center">{{ number_format($summary->total_edit_collect_weight) }}</td>
                            <td class="px-4 py-3 text-sm text-yellow-600 dark:text-yellow-400 text-center">{{ number_format($summary->total_edit_weight ?? 0) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">
                                {{ number_format($summary->total_collect_weight > 0 ? $summary->total_edit_collect_weight*100/$summary->total_collect_weight : 0) }}%
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ number_format($summary->total_billing_weight) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ number_format($summary->total_billing_weight - $summary->total_collect_weight) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="mt-4">
                {!! $operations->withQueryString()->links() !!}
            </div>
        </div>
    </div>
</x-menu.page-layout>
@endsection
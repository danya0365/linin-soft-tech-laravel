@extends('layouts.supervisor')

@section('content')
<x-supervisor.page 
    title="{{ __('ประวัติบิลรายรับ') }}"
    subtitle="Billing Logs 1" 
    icon="fa-history"
    :breadcrumbs="[
        ['label' => __('Customer'), 'route' => route('supervisor.customer')],
        ['label' => __('ประวัติบิลรายรับ')]
    ]"
>
    {{-- Success Message --}}
    @if ($message = Session::get('success'))
    <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 flex items-center">
        <i class="fa fa-check-circle mr-2"></i>
        {{ $message }}
    </div>
    @endif

    {{-- Filters --}}
    <x-supervisor.date-filter 
        :action="request()->url()" 
        :date-start-at="$dateStartAt" 
        :date-end-at="$dateEndAt" 
        :reset-url="request()->url()"
    >
        <div class="md:col-span-1">
            <x-supervisor.select-group-filter 
                name="customer_id" 
                label="ลูกค้า" 
                :options="$customerGroups" 
                :selected="$customerIdSelected" 
                group-label-key="name"
                group-options-key="customers"
                option-value-key="id"
                option-label-key="name"
            />
        </div>
        <div class="md:col-span-1">
             <x-supervisor.select-filter 
                name="sort_order" 
                label="เรียงโดย" 
                :options="$sortOrders" 
                :selected="$sortOrderSelected" 
                show-all="false"
            />
        </div>
    </x-supervisor.date-filter>

    {{-- Helper function for percent color --}}
    @php
        function getPercentColor($percent) {
            if ($percent === '-') return '';
            $val = floatval($percent);
            if ($val > 20) return 'text-red-600 dark:text-red-400 font-bold';
            if ($val > 15) return 'text-amber-600 dark:text-amber-400 font-bold';
            return 'text-green-600 dark:text-green-400 font-bold';
        }
    @endphp

    {{-- Main Logs Table --}}
    <div class="mb-8">
        <x-supervisor.card title="{{ __('ประวัติบิลรายรับ - Billing Logs') }}">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 whitespace-nowrap">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">วันที่บันทึก</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ลูกค้า</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">น้ำหนัก (kg.)</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">เปียก (kg.)</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">แห้ง (kg.)</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">% หักลบ</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                แก้ไข (kg.) 
                                <i class="fa fa-info-circle text-gray-400" title="ข้อมูลกรอกโดย Supervisor อาจไม่ตรงกับข้อมูล Operation จริง"></i>
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">% แก้ไข</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">จำนวนเงิน (฿)</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">วันเก็บเงิน</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($billingLogs as $billingLog)
                            @php
                                $wetWeight = $billingLog->total_wet_weight ?? 0;
                                $dryWeight = $billingLog->total_dry_weight ?? 0;
                                $editWeight = $billingLog->total_edit_weight ?? 0;
                                $billingWeight = $billingLog->total_billing_weight ?? 0;
                                $diffPercent = ($wetWeight > 0 && $dryWeight > 0) 
                                    ? round(($wetWeight - $dryWeight) / $wetWeight * 100, 2) 
                                    : '-';
                                $editPercent = ($billingWeight > 0 && $editWeight > 0)
                                    ? round(($editWeight / $billingWeight) * 100, 2)
                                    : '-';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $billingLog->created_at->format('Y-m-d') }}</td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $billingLog->customer->name ?? '-' }}</td>
                                <td class="px-4 py-4 text-sm text-right text-gray-900 dark:text-gray-100 font-medium">{{ number_format($billingLog->total_billing_weight) }}</td>
                                <td class="px-4 py-4 text-sm text-right text-gray-500 dark:text-gray-400">{{ $wetWeight ? number_format($wetWeight, 2) : '-' }}</td>
                                <td class="px-4 py-4 text-sm text-right text-gray-500 dark:text-gray-400">{{ $dryWeight ? number_format($dryWeight, 2) : '-' }}</td>
                                <td class="px-4 py-4 text-sm text-right {{ getPercentColor($diffPercent) }}">
                                    {{ $diffPercent !== '-' ? $diffPercent . '%' : '-' }}
                                </td>
                                <td class="px-4 py-4 text-sm text-right text-gray-900 dark:text-gray-100">{{ $editWeight ? number_format($editWeight, 2) : '-' }}</td>
                                <td class="px-4 py-4 text-sm text-right text-blue-500 dark:text-blue-400 font-medium">
                                    {{ $editPercent !== '-' ? $editPercent . '%' : '-' }}
                                </td>
                                <td class="px-4 py-4 text-sm text-right text-indigo-600 dark:text-indigo-400 font-bold">{{ number_format($billingLog->total_billing_payment) }}</td>
                                <td class="px-4 py-4 text-sm text-right text-gray-500 dark:text-gray-400">{{ $billingLog->billing_payment_date }}</td>
                                <td class="px-4 py-4 text-sm text-center">
                                        <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('supervisor.customer.billing-logs.edit', $billingLog->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form class="delete-form" action="{{ route('supervisor.customer.billing-logs.delete', $billingLog->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination --}}
            <div class="mt-4">
                {{ $billingLogs->withQueryString()->links() }}
            </div>
        </x-supervisor.card>
    </div>

    {{-- Summary Table --}}
    <x-supervisor.card title="สรุปยอดรวม (Summary)">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ลูกค้า</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">น้ำหนักรวม (kg.)</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">จำนวนเงินรวม (฿)</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $sum_total_billing_weight = $sum_total_billing_payment = 0;
                    @endphp
                    @foreach ($billingSums as $billingSum)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $billingSum->customer->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            {{ number_format($billingSum->total_billing_weight) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-indigo-600 dark:text-indigo-400 font-bold">
                            {{ number_format($billingSum->total_billing_payment) }}
                        </td>
                    </tr>
                    @php
                        $sum_total_billing_weight += $billingSum->total_billing_weight;
                        $sum_total_billing_payment += $billingSum->total_billing_payment;
                    @endphp
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700 font-bold">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">ยอดรวมทั้งสิ้น:</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">{{ number_format($sum_total_billing_weight) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-indigo-600 dark:text-indigo-400">{{ number_format($sum_total_billing_payment) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-supervisor.card>
</x-supervisor.page>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') return;
    $(function(){
        $('.delete-form').on('submit', function(e){
            if (!confirm("Are you sure?")) {
                return false;
            }
            return true
        })
    });
});
</script>
@endpush
@endsection
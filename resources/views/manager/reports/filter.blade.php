@extends('layouts.manager')

@section('content')
<x-manager.page 
    title="{{ __('รายงานสถิติ - Report') }}"
    :breadcrumbs="[
        ['label' => 'รายงานสถิติ']
    ]"
>
    <div class="space-y-6">
        {{-- Operation Statistics Summary --}}
        @php
            $operationStats = App\Managers\HighChartManager::getOperationStatsSummary();
        @endphp
        <x-manager.card 
            title="🧺 สรุป Operation (7 วันล่าสุด)" 
            header-class="bg-cyan-600 text-white"
        >
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <x-manager.stat-card 
                    title="จำนวนงาน" 
                    value="{{ number_format($operationStats['operationCount']) }}" 
                    unit="รายการ" 
                    color="indigo" 
                />
                <x-manager.stat-card 
                    title="ผ้าเปียก" 
                    value="{{ number_format($operationStats['totalWetWeight'], 2) }}" 
                    unit="กก." 
                    color="cyan" 
                />
                <x-manager.stat-card 
                    title="ผ้าแห้ง" 
                    value="{{ number_format($operationStats['totalDryWeight'], 2) }}" 
                    unit="กก." 
                    color="amber" 
                />
                @php
                    $diffPercent = $operationStats['weightDiffPercent'];
                    $diffColor = $diffPercent > 20 ? 'danger' : ($diffPercent > 15 ? 'warning' : 'success');
                    $twDiffColor = match($diffColor) {
                        'danger' => 'red',
                        'warning' => 'amber',
                        default => 'emerald'
                    };
                @endphp
                <x-manager.stat-card 
                    title="% หักลบ" 
                    value="{{ $diffPercent }}%" 
                    unit="เปียก-แห้ง" 
                    color="{{ $twDiffColor }}" 
                />
                <x-manager.stat-card 
                    title="ผ้าแก้ไข (ระบบ)" 
                    value="{{ number_format($operationStats['totalEditCollectWeight'] ?? 0, 2) }}" 
                    unit="กก. | {{ $operationStats['editCollectWeightPercent'] ?? 0 }}%" 
                    color="amber" 
                    bg-class="bg-amber-50 dark:bg-amber-900/20"
                />
                <x-manager.stat-card 
                    title="ผ้าแก้ไข (กรอกมือ)" 
                    value="{{ number_format($operationStats['totalEditWeight'], 2) }}" 
                    unit="กก. | {{ $operationStats['editWeightPercent'] }}%" 
                    color="cyan" 
                    bg-class="bg-cyan-50 dark:bg-cyan-900/20"
                />
                <x-manager.stat-card 
                    title="น้ำหนักบิล" 
                    value="{{ number_format($operationStats['totalBillingWeight'], 2) }}" 
                    unit="กก." 
                    color="gray" 
                />
                <x-manager.stat-card 
                    title="ยอดบิล" 
                    value="{{ number_format($operationStats['totalBillingPayment'], 2) }}" 
                    unit="บาท" 
                    color="emerald" 
                />
            </div>
        </x-manager.card>

        {{-- Sales Range Days Chart --}}
        <x-manager.card title="ต้นทุน, ยอดขาย, ตามวันที่เลือก">
            <x-manager.date-filter 
                action="{{ request()->url() }}" 
                id="sales-range-days-form"
                start-name="sales-range-days-start-at"
                end-name="sales-range-days-end-at"
            />
            <div id="sales-range-days-chart" class="w-full h-[400px]"></div>
        </x-manager.card>

        {{-- Energy Range Days Chart --}}
        <x-manager.card title="พลังงานตามวันที่เลือก">
            <x-manager.date-filter 
                action="{{ request()->url() }}" 
                id="energy-range-days-form"
                start-name="energy-range-days-start-at"
                end-name="energy-range-days-end-at"
            />
            <div id="energy-range-days-chart" class="w-full h-[400px]"></div>
        </x-manager.card>

        {{-- Expense Range Days --}}
        <x-manager.card title="ต้นทุนตามวันที่เลือก">
            <x-manager.date-filter 
                 action="{{ request()->url() }}" 
                id="expense-range-days-form"
                start-name="expense-range-days-start-at"
                end-name="expense-range-days-end-at"
            />
            <div id="expense-range-days-chart" class="w-full h-[400px]"></div>
        </x-manager.card>

        {{-- Income Range Days --}}
        <x-manager.card title="ยอดขายตามวันที่เลือก">
             <x-manager.date-filter 
                action="{{ request()->url() }}" 
                id="income-range-days-form"
                start-name="income-range-days-start-at"
                end-name="income-range-days-end-at"
            />
            <div id="income-range-days-chart" class="w-full h-[400px]"></div>
        </x-manager.card>
    </div>
</x-manager.page>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') return;

    $(function(){

        // Datepicker สำหรับกราฟเปรียบเทียบ
        //$('[name=compare-start]').datepicker({ format: 'yyyy-mm-dd' });
        //$('[name=compare-end]').datepicker({ format: 'yyyy-mm-dd' });

        $(function(){
            var startAt = '{{ request()->get('sales-range-days-start-at') }}'
            var endAt = '{{ request()->get('sales-range-days-end-at') }}'
            var salesLatestDaysSummary = @json(App\Managers\HighChartManager::getSalesLatestDaysSummary(request()->get('sales-range-days-start-at'), request()->get('sales-range-days-end-at')));
            var title = "ต้นทุน, ยอดขาย, กำไร 7 วันล่าสุด"
            if ( startAt && endAt ) {
                $('[name=sales-range-days-start-at]').val(startAt);
                $('[name=sales-range-days-end-at]').val(endAt);
                title = `ต้นทุน, ยอดขาย, กำไร วันที่ ${startAt} ถึง ${endAt}`
            }
            $.salesLatestDaysChart({ 'renderTo': 'sales-range-days-chart', 'data': salesLatestDaysSummary, 'title': title});
        })


        $(function(){
            var startAt = '{{ request()->get('energy-range-days-start-at') }}'
            var endAt = '{{ request()->get('energy-range-days-end-at') }}'
            var energyDaysSummary = @json(App\Managers\HighChartManager::getEnergyDaysSummary(request()->get('energy-range-days-start-at'), request()->get('energy-range-days-end-at')));
            var title = "ยอดการใช้พลังงาน 7 วันล่าสุด"
            if ( startAt && endAt ) {
                $('[name=energy-range-days-start-at]').val(startAt);
                $('[name=energy-range-days-end-at]').val(endAt);
                title = `ยอดการใช้พลังงาน วันที่ ${startAt} ถึง ${endAt}`
            }
            $.energyDaysChart({ 'renderTo': 'energy-range-days-chart', 'data': energyDaysSummary, 'title': title});
        })

        $(function(){
            var startAt = '{{ request()->get('expense-range-days-start-at') }}'
            var endAt = '{{ request()->get('expense-range-days-end-at') }}'
            var expenseDaysSummary = @json(App\Managers\HighChartManager::getExpenseDaysSummary(request()->get('expense-range-days-start-at'), request()->get('expense-range-days-end-at')));
            var title = "ยอดต้นทุน 7 วันล่าสุด"
            if ( startAt && endAt ) {
                $('[name=expense-range-days-start-at]').val(startAt);
                $('[name=expense-range-days-end-at]').val(endAt);
                title = `ยอดต้นทุน วันที่ ${startAt} ถึง ${endAt}`
            }
            $.expenseDaysChart({ 'renderTo': 'expense-range-days-chart', 'data': expenseDaysSummary, 'title': title});
        })

        $(function(){
            var startAt = '{{ request()->get('income-range-days-start-at') }}'
            var endAt = '{{ request()->get('income-range-days-end-at') }}'
            var incomeDaysSummary = @json(App\Managers\HighChartManager::getIncomeDaysSummary(request()->get('income-range-days-start-at'), request()->get('income-range-days-end-at')));
            var title = "ยอดขาย 7 วันล่าสุด"
            if ( startAt && endAt ) {
                $('[name=income-range-days-start-at]').val(startAt);
                $('[name=income-range-days-end-at]').val(endAt);
                title = `ยอดขาย วันที่ ${startAt} ถึง ${endAt}`
            }
            $.incomeDaysChart({ 'renderTo': 'income-range-days-chart', 'data': incomeDaysSummary, 'title': title});
        })

        /*
        $('[name=energy-range-days-start-at]').datepicker({ format: 'yyyy-mm-dd' });
        $('[name=energy-range-days-end-at]').datepicker({ format: 'yyyy-mm-dd' });

        $('[name=expense-range-days-start-at]').datepicker({ format: 'yyyy-mm-dd' });
        $('[name=expense-range-days-end-at]').datepicker({ format: 'yyyy-mm-dd' });

        $('[name=sales-range-days-start-at]').datepicker({ format: 'yyyy-mm-dd' });
        $('[name=sales-range-days-end-at]').datepicker({ format: 'yyyy-mm-dd' });

        $('[name=income-range-days-start-at]').datepicker({ format: 'yyyy-mm-dd' });
        $('[name=income-range-days-end-at]').datepicker({ format: 'yyyy-mm-dd' });
        */

    })
});
</script>
@endpush
@endsection
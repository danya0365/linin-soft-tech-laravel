@extends('layouts.manager')

@section('content')
<x-manager.page 
    title="{{ __('รายงานสถิติ - Report') }}"
    :breadcrumbs="[
        ['label' => 'รายงานสถิติ']
    ]"
>
    <div class="space-y-6">
        {{-- Overall Summary --}}
        <x-manager.card title="ภาพรวม">
            <div class="mb-6 flex justify-center">
                <form class="flex flex-wrap items-end gap-2" action="{{ request()->url() }}" method="GET">
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <input type="date" id="overall_date_start_at" name="overall[date_start_at]" value="{{ $overallSummary['queryParam']['dateStartAt'] }}" class="form-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm" placeholder="วันที่เริ่ม">
                        </div>
                        <span class="text-gray-500">ถึง</span>
                        <div class="relative">
                             <input type="date" id="overall_date_end_at" name="overall[date_end_at]" value="{{ $overallSummary['queryParam']['dateEndAt'] }}" class="form-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm" placeholder="วันที่สิ้นสุด">
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Submit</button>
                        <a href="{{ route('manager.report') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300">Reset</a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <p class="text-lg text-gray-500 dark:text-gray-400 mb-1">ต้นทุน</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($overallSummary['data']['expense']) }}</p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <p class="text-lg text-gray-500 dark:text-gray-400 mb-1">ยอดขาย</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($overallSummary['data']['income']) }}</p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <p class="text-lg text-gray-500 dark:text-gray-400 mb-1">กำไร</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($overallSummary['data']['profit']) }}</p>
                </div>
            </div>
        </x-manager.card>

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

        {{-- Monthly Chart --}}
        <x-manager.card title="ต้นทุน, ยอดขาย, กำไร ต่อเดือน">
            <div id="sales-bar-chart" class="w-full h-[400px]"></div>
        </x-manager.card>

        {{-- Latest 7 Days Chart --}}
        <x-manager.card title="ต้นทุน, ยอดขาย, กำไร 7 วันล่าสุด">
            <div id="sales-latest-days-chart" class="w-full h-[400px]"></div>
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

        {{-- Energy Pie Chart --}}
        <x-manager.card title="ภาพรวมพลังงาน">
            <h5 class="text-center font-medium text-gray-700 dark:text-gray-300 mb-4">Summary</h5>
            <div class="flex justify-center mb-6">
                <form class="flex flex-wrap items-end gap-2" action="{{ request()->url() }}" method="GET">
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <input type="date" id="energy_date_start_at" name="energy[date_start_at]" value="{{ $energySummary['queryParam']['dateStartAt'] }}" class="form-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm" placeholder="วันที่เริ่ม">
                        </div>
                        <span class="text-gray-500">ถึง</span>
                        <div class="relative">
                             <input type="date" id="energy_date_end_at" name="energy[date_end_at]" value="{{ $energySummary['queryParam']['dateEndAt'] }}" class="form-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm" placeholder="วันที่สิ้นสุด">
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Submit</button>
                        <a href="{{ route('manager.report') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300">Reset</a>
                    </div>
                </form>
            </div>
            <div id="energy-pie-chart" class="w-full h-[400px]"></div>
        </x-manager.card>

        {{-- Energy Latest Day Chart --}}
        <x-manager.card title="พลังงานแต่ละวัน">
             <div id="energy-latest-day-chart" class="w-full h-[400px]"></div>
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

        {{-- Expense Latest Day --}}
        <x-manager.card title="ต้นทุนแต่ละวัน">
            <div id="expense-latest-day-chart" class="w-full h-[400px]"></div>
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

        {{-- Income Latest Day --}}
        <x-manager.card title="ยอดขายแต่ละวัน">
             <div id="income-latest-day-chart" class="w-full h-[400px]"></div>
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

        var salesYearSummary = @json(App\Managers\HighChartManager::getSalesYearSummary());
        $.salesChart({ 'renderTo': "sales-bar-chart", 'data': salesYearSummary});

        var energySummary = @json($energySummary);
        $.energyPieChart({ 'renderTo': "energy-pie-chart", 'data': energySummary.data});
        
        var energyDaysSummary = @json(App\Managers\HighChartManager::getEnergyDaysSummary());
        $.energyDaysChart({ 'renderTo': 'energy-latest-day-chart', 'data': energyDaysSummary, 'title': "ยอดการใช้พลังงาน 7 วันล่าสุด"});

        var expenseDaysSummary = @json(App\Managers\HighChartManager::getExpenseDaysSummary());
        $.expenseDaysChart({ 'renderTo': 'expense-latest-day-chart', 'data': expenseDaysSummary, 'title': "ยอดต้นทุน 7 วันล่าสุด"});

        var salesLatestDaysSummary = @json(App\Managers\HighChartManager::getSalesLatestDaysSummary());
        $.salesLatestDaysChart({ 'renderTo': 'sales-latest-days-chart', 'data': salesLatestDaysSummary, 'title': "ต้นทุน, ยอดขาย, กำไร 7 วันล่าสุด"});

        var incomeDaysSummary = @json(App\Managers\HighChartManager::getIncomeDaysSummary());
        $.incomeDaysChart({ 'renderTo': 'income-latest-day-chart', 'data': incomeDaysSummary, 'title': "ยอดขาย 7 วันล่าสุด"});

        var salesRangeDaysSummary = () => {
            var startAt = $('[name=sales-range-days-start-at]').val();
            var endAt = $('[name=sales-range-days-end-at]').val();

            var url = '{!! route('api.sales-range-days-chart', ['startAt' => 'startAtParam', 'endAt' => 'endAtParam']) !!}';
            
            url = url.replace('startAtParam', startAt)
            url = url.replace('endAtParam', endAt)

            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                },
            });

            $.get(url, function(response){
                $.salesLatestDaysChart({ 'renderTo': 'sales-range-days-chart', 'data': response, 'title': `ต้นทุน, ยอดขาย, กำไร วันที่ ${startAt} ถึง ${endAt}`});
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    text: 'กรุณาลองใหม่อีกครั้ง'
                })
            }).always(function() {
                Swal.close();
            });
        }

        $('#sales-range-days-form').on('submit', (e) => {
            e.preventDefault();
            salesRangeDaysSummary();
        })

        var energyRangeDaysSummary = () => {
            var startAt = $('[name=energy-range-days-start-at]').val();
            var endAt = $('[name=energy-range-days-end-at]').val();

            var url = '{!! route('api.energy-range-days-chart', ['startAt' => 'startAtParam', 'endAt' => 'endAtParam']) !!}';
            
            url = url.replace('startAtParam', startAt)
            url = url.replace('endAtParam', endAt)

            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                },
            });

            $.get(url, function(response){
                $.energyDaysChart({ 'renderTo': 'energy-range-days-chart', 'data': response, 'title': `ยอดการใช้พลังงาน วันที่ ${startAt} ถึง ${endAt}`});
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    text: 'กรุณาลองใหม่อีกครั้ง'
                })
            }).always(function() {
                Swal.close();
            });
        }

        $('#energy-range-days-form').on('submit', (e) => {
            e.preventDefault();
            energyRangeDaysSummary();
        })

        var expenseRangeDaysSummary = () => {
            var startAt = $('[name=expense-range-days-start-at]').val();
            var endAt = $('[name=expense-range-days-end-at]').val();

            var url = '{!! route('api.expense-range-days-chart', ['startAt' => 'startAtParam', 'endAt' => 'endAtParam']) !!}';
            
            url = url.replace('startAtParam', startAt)
            url = url.replace('endAtParam', endAt)

            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                },
            });

            $.get(url, function(response){
                $.expenseDaysChart({ 'renderTo': 'expense-range-days-chart', 'data': response, 'title': `ยอดต้นทุน วันที่ ${startAt} ถึง ${endAt}`});
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    text: 'กรุณาลองใหม่อีกครั้ง'
                })
            }).always(function() {
                Swal.close();
            });
        }

        $('#expense-range-days-form').on('submit', (e) => {
            e.preventDefault();
            expenseRangeDaysSummary();
        })

        var incomeRangeDaysSummary = () => {
            var startAt = $('[name=income-range-days-start-at]').val();
            var endAt = $('[name=income-range-days-end-at]').val();

            var url = '{!! route('api.income-range-days-chart', ['startAt' => 'startAtParam', 'endAt' => 'endAtParam']) !!}';
            
            url = url.replace('startAtParam', startAt)
            url = url.replace('endAtParam', endAt)

            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                },
            });

            $.get(url, function(response){
                $.incomeDaysChart({ 'renderTo': 'income-range-days-chart', 'data': response, 'title': `ยอดขาย วันที่ ${startAt} ถึง ${endAt}`});
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    text: 'กรุณาลองใหม่อีกครั้ง'
                })
            }).always(function() {
                Swal.close();
            });
        }

        $('#income-range-days-form').on('submit', (e) => {
            e.preventDefault();
            incomeRangeDaysSummary();
        })
        
        // Remove datepicker init since we use native date input or it might conflict
        // But the previous code used bootstrap datepicker.
        // If the browser supports type="date", we don't need datepicker.
        // If we want to keep datepicker, we'd need to init it on the new inputs.
        // For modern refactor, relying on browser date input is better (native UI).
        // I will comment out the datepicker inits to let native picker work.
        
        /*
        $('[name=energy-range-days-start-at]').datepicker({ format: 'yyyy-mm-dd' });
        $('[name=energy-range-days-end-at]').datepicker({ format: 'yyyy-mm-dd' });

        $('[name=expense-range-days-start-at]').datepicker({ format: 'yyyy-mm-dd' });
        $('[name=expense-range-days-end-at]').datepicker({ format: 'yyyy-mm-dd' });

        $('[name=sales-range-days-start-at]').datepicker({ format: 'yyyy-mm-dd' });
        $('[name=sales-range-days-end-at]').datepicker({ format: 'yyyy-mm-dd' });

        $('[name=income-range-days-start-at]').datepicker({ format: 'yyyy-mm-dd' });
        $('[name=income-range-days-end-at]').datepicker({ format: 'yyyy-mm-dd' });

        $('#overall_date_start_at').datepicker({ format: 'yyyy-mm-dd' });
        $('#overall_date_end_at').datepicker({ format: 'yyyy-mm-dd' });

        $('#energy_date_start_at').datepicker({ format: 'yyyy-mm-dd' });
        $('#energy_date_end_at').datepicker({ format: 'yyyy-mm-dd' });
        */
    })
});
</script>
@endpush
@endsection
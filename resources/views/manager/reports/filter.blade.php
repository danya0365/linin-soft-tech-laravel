@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manager') }}">Manager</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('รายงานสถิติ - Report') }}</li>
        </ol>
    </nav>

    {{-- 3 กราฟเปรียบเทียบใหม่ --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">📊 กราฟเปรียบเทียบภาพรวม (เลือกวันที่ร่วมกัน)</div>
                <div class="card-body">
                    <form id="comparison-form" class="row row-cols-lg-auto g-3 align-items-center mb-3" action="{{ request()->url() }}" method="GET">
                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" name="compare-start" value="" class="form-control" placeholder="วันที่เริ่ม">
                                <span class="input-group-text">ถึง</span>
                                <input type="text" name="compare-end" value="" class="form-control" placeholder="วันที่สิ้นสุด">
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">ดูกราฟ</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- กราฟ 1: ภาพรวมการเงิน --}}
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">📊 ภาพรวมการเงิน (บาท)</div>
                <div class="card-body">
                    <div id="financial-comparison-chart" style="min-width: 100%; height: 350px;"></div>
                </div>
            </div>
        </div>
        {{-- กราฟ 2: ปริมาณงาน --}}
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">📦 ปริมาณงาน (กก.)</div>
                <div class="card-body">
                    <div id="operation-comparison-chart" style="min-width: 100%; height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- กราฟ 3: เปรียบเทียบแนวโน้ม --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">📈 เปรียบเทียบแนวโน้ม (Normalized %)</div>
                <div class="card-body">
                    <div id="trend-comparison-chart" style="min-width: 100%; height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Operation Statistics Summary Card --}}
    @php
        $operationStats = App\Managers\HighChartManager::getOperationStatsSummary(request()->get('compare-start'), request()->get('compare-end'));
    @endphp
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    🧺 สรุป Operation 
                    @if(request()->get('compare-start') && request()->get('compare-end'))
                        ({{ request()->get('compare-start') }} ถึง {{ request()->get('compare-end') }})
                    @else
                        (7 วันล่าสุด)
                    @endif
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h5 class="text-muted mb-1">📋 จำนวนงาน</h5>
                                <h3 class="text-primary mb-0">{{ number_format($operationStats['operationCount']) }}</h3>
                                <small class="text-muted">รายการ</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h5 class="text-muted mb-1">💧 ผ้าเปียก</h5>
                                <h3 class="text-info mb-0">{{ number_format($operationStats['totalWetWeight'], 2) }}</h3>
                                <small class="text-muted">กก.</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h5 class="text-muted mb-1">☀️ ผ้าแห้ง</h5>
                                <h3 class="text-warning mb-0">{{ number_format($operationStats['totalDryWeight'], 2) }}</h3>
                                <small class="text-muted">กก.</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h5 class="text-muted mb-1">📉 % หักลบ</h5>
                                @php
                                    $diffPercent = $operationStats['weightDiffPercent'];
                                    $diffColor = $diffPercent > 20 ? 'danger' : ($diffPercent > 15 ? 'warning' : 'success');
                                @endphp
                                <h3 class="text-{{ $diffColor }} mb-0">{{ $diffPercent }}%</h3>
                                <small class="text-muted">เปียก-แห้ง</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h5 class="text-muted mb-1">⚖️ น้ำหนักบิล</h5>
                                <h3 class="text-secondary mb-0">{{ number_format($operationStats['totalBillingWeight'], 2) }}</h3>
                                <small class="text-muted">กก.</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h5 class="text-muted mb-1">💵 ยอดบิล</h5>
                                <h3 class="text-success mb-0">{{ number_format($operationStats['totalBillingPayment'], 2) }}</h3>
                                <small class="text-muted">บาท</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">ต้นทุน, ยอดขาย, ตามวันที่เลือก</div>
                <div class="card-body text-center">
                    <form id="sales-range-days-form" class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ request()->url() }}" method="GET">

                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" name="sales-range-days-start-at" value="" class="form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="text" name="sales-range-days-end-at" value="" class="form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </form>
                    <div id="sales-range-days-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">พลังงานตามวันที่เลือก</div>
                <div class="card-body text-center">
                    <form id="energy-range-days-form" class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ request()->url() }}" method="GET">

                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" name="energy-range-days-start-at" value="" class="form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="text" name="energy-range-days-end-at" value="" class="form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </form>
                    <div id="energy-range-days-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">ต้นทุนตามวันที่เลือก</div>
                <div class="card-body text-center">
                    <form id="expense-range-days-form" class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ request()->url() }}" method="GET">

                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" name="expense-range-days-start-at" value="" class="form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="text" name="expense-range-days-end-at" value="" class="form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </form>
                    <div id="expense-range-days-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">ยอดขายตามวันที่เลือก</div>
                <div class="card-body text-center">
                    <form id="income-range-days-form" class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ request()->url() }}" method="GET">

                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" name="income-range-days-start-at" value="" class="date form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="text" name="income-range-days-end-at" value="" class="date form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </form>
                    <div id="income-range-days-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){

    // 3 กราฟเปรียบเทียบใหม่
    $(function(){
        var compareStart = '{{ request()->get('compare-start') }}';
        var compareEnd = '{{ request()->get('compare-end') }}';

        // กราฟ 1: ภาพรวมการเงิน
        var financialData = @json(App\Managers\HighChartManager::getFinancialComparisonSummary(request()->get('compare-start'), request()->get('compare-end')));
        var financialTitle = "ภาพรวมการเงิน 7 วันล่าสุด";
        if (compareStart && compareEnd) {
            $('[name=compare-start]').val(compareStart);
            $('[name=compare-end]').val(compareEnd);
            financialTitle = `ภาพรวมการเงิน ${compareStart} ถึง ${compareEnd}`;
        }
        $.comparisonLineChart({
            renderTo: 'financial-comparison-chart',
            data: financialData,
            title: financialTitle,
            yAxisLabel: 'จำนวนเงิน (บาท)',
            unit: 'บาท'
        });

        // กราฟ 2: ปริมาณงาน
        var operationData = @json(App\Managers\HighChartManager::getOperationComparisonSummary(request()->get('compare-start'), request()->get('compare-end')));
        var operationTitle = "ปริมาณงาน 7 วันล่าสุด";
        if (compareStart && compareEnd) {
            operationTitle = `ปริมาณงาน ${compareStart} ถึง ${compareEnd}`;
        }
        $.comparisonLineChart({
            renderTo: 'operation-comparison-chart',
            data: operationData,
            title: operationTitle,
            yAxisLabel: 'น้ำหนัก (กก.)',
            unit: 'กก.'
        });

        // กราฟ 3: เปรียบเทียบแนวโน้ม (Normalized %)
        var trendData = @json(App\Managers\HighChartManager::getTrendComparisonSummary(request()->get('compare-start'), request()->get('compare-end')));
        var trendTitle = "เปรียบเทียบแนวโน้ม 7 วันล่าสุด";
        if (compareStart && compareEnd) {
            trendTitle = `เปรียบเทียบแนวโน้ม ${compareStart} ถึง ${compareEnd}`;
        }
        $.trendLineChart({
            renderTo: 'trend-comparison-chart',
            data: trendData,
            title: trendTitle
        });
    });

    // Datepicker สำหรับกราฟเปรียบเทียบ
    $('[name=compare-start]').datepicker({ format: 'yyyy-mm-dd' });
    $('[name=compare-end]').datepicker({ format: 'yyyy-mm-dd' });

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

    $('[name=energy-range-days-start-at]').datepicker({ format: 'yyyy-mm-dd' });
    $('[name=energy-range-days-end-at]').datepicker({ format: 'yyyy-mm-dd' });

    $('[name=expense-range-days-start-at]').datepicker({ format: 'yyyy-mm-dd' });
    $('[name=expense-range-days-end-at]').datepicker({ format: 'yyyy-mm-dd' });

    $('[name=sales-range-days-start-at]').datepicker({ format: 'yyyy-mm-dd' });
    $('[name=sales-range-days-end-at]').datepicker({ format: 'yyyy-mm-dd' });

    $('[name=income-range-days-start-at]').datepicker({ format: 'yyyy-mm-dd' });
    $('[name=income-range-days-end-at]').datepicker({ format: 'yyyy-mm-dd' });

})
</script>
@endsection
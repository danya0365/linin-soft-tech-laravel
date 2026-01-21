@extends('layouts.manager')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manager') }}">Manager</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('รายงานสถิติ - Report') }}</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">ภาพรวม</div>
                <div class="card-body text-center">
                    <form class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ request()->url() }}" method="GET">

                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" id="overall_date_start_at" name="overall[date_start_at]" value="{{ $overallSummary['queryParam']['dateStartAt'] }}" class="form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="text" id="overall_date_end_at" name="overall[date_end_at]" value="{{ $overallSummary['queryParam']['dateEndAt'] }}" class="form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('manager.report') }}" role="button" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>


                    <div class="row g-2">
                        <div class="col-4">
                            <div class="card text-center">
                                <div class="card-header">
                                    <p class="fs-3 m-0">ต้นทุน</p>
                                </div>
                                <div class="card-body">
                                    <p class="fs-1 m-0">{{ number_format($overallSummary['data']['expense']) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card text-center">
                                <div class="card-header">
                                    <p class="fs-3 m-0">ยอดขาย</p>
                                </div>
                                <div class="card-body">
                                    <p class="fs-1 m-0">{{ number_format($overallSummary['data']['income']) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card text-center">
                                <div class="card-header">
                                    <p class="fs-3 m-0">กำไร</p>
                                </div>
                                <div class="card-body">
                                    <p class="fs-1 m-0">{{ number_format($overallSummary['data']['profit']) }}</p>
                                </div>
                            </div>
                        </div>
                      </div>
                </div>
            </div>
        </div>

        {{-- Operation Statistics Summary Card --}}
        @php
            $operationStats = App\Managers\HighChartManager::getOperationStatsSummary();
        @endphp
        <div class="col-md-12 m-2">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    🧺 สรุป Operation (7 วันล่าสุด)
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
                                <h5 class="text-muted mb-1">✂️ ผ้าแก้ไข</h5>
                                <h3 class="text-info mb-0">{{ number_format($operationStats['totalEditWeight'], 2) }}</h3>
                                <small class="text-muted">กก.</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h5 class="text-muted mb-1">📊 % ผ้าแก้ไข</h5>
                                <h3 class="text-primary mb-0">{{ $operationStats['editWeightPercent'] }}%</h3>
                                <small class="text-muted">แก้ไข/บิล</small>
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

        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">ต้นทุน, ยอดขาย, กำไร ต่อเดือน</div>
                <div class="card-body text-center">
                    <div id="sales-bar-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">ต้นทุน, ยอดขาย, กำไร 7 วันล่าสุด</div>
                <div class="card-body text-center">
                    <div id="sales-latest-days-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

                    </div>
                </div>
            </div>
        </div>
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
                <div class="card-header">ภาพรวมพลังงาน</div>
                <div class="card-body text-center">
                    <h5 class="card-title">Summary</h5>
                    <form class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ request()->url() }}" method="GET">

                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" id="energy_date_start_at" name="energy[date_start_at]" value="{{ $energySummary['queryParam']['dateStartAt'] }}" class="form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="text" id="energy_date_end_at" name="energy[date_end_at]" value="{{ $energySummary['queryParam']['dateEndAt'] }}" class="form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('manager.report') }}" role="button" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>


                    <div id="energy-pie-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">พลังงานแต่ละวัน</div>
                <div class="card-body text-center">

                    <div id="energy-latest-day-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

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
                <div class="card-header">ต้นทุนแต่ละวัน</div>
                <div class="card-body text-center">

                    <div id="expense-latest-day-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

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
                <div class="card-header">ยอดขายแต่ละวัน</div>
                <div class="card-body text-center">

                    <div id="income-latest-day-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

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
})
</script>
@endsection
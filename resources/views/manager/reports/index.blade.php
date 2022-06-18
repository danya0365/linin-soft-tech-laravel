@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('supervisor') }}">Supervisor</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('รายงานสถิติ - Report') }}</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
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
                <div class="card-header">ภาพรวมพลังงาน</div>
                <div class="card-body text-center">
                    <h5 class="card-title">Summary</h5>
                    <form class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ request()->url() }}" method="GET">

                        <div class="col-12">
                            <div class="input-group">
                                <input type="date" name="energy[date_start_at]" value="{{ $energySummary['queryParam']['dateStartAt'] }}" class="form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="date" name="energy[date_end_at]" value="{{ $energySummary['queryParam']['dateEndAt'] }}" class="form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
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

                    <div id="energy-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">ต้นทุนแต่ละวัน</div>
                <div class="card-body text-center">

                    <div id="expense-day-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    var salesYearSummary = @json($salesYearSummary);
    $.salesChart({ 'renderTo': "sales-bar-chart", 'data': salesYearSummary});
    
    var energySummary = @json($energySummary);
    $.energyPieChart({ 'renderTo': "energy-pie-chart", 'data': energySummary.data});
    
    var energyDaysSummary = @json(App\Managers\HighChartManager::getEnergyDaysSummary());
    $.energyDaysChart({ 'renderTo': 'energy-chart', 'data': energyDaysSummary});

    var expenseDaysSummary = @json(App\Managers\HighChartManager::getExpenseDaysSummary());
    $.expenseDaysChart({ 'renderTo': 'expense-day-chart', 'data': expenseDaysSummary});

    var salesLatestDaysSummary = @json(App\Managers\HighChartManager::getSalesLatestDaysSummary());
    $.salesLatestDaysChart({ 'renderTo': 'sales-latest-days-chart', 'data': salesLatestDaysSummary});
})
</script>
@endsection
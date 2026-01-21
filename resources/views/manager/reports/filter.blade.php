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
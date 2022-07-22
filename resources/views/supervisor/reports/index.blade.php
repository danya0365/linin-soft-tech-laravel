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
    </div>
</div>
<script>
$(function(){

    var expenseDaysSummary = @json(App\Managers\HighChartManager::getExpenseDaysSummary());
    $.expenseDaysChart({ 'renderTo': 'expense-latest-day-chart', 'data': expenseDaysSummary});

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

    $('[name=expense-range-days-start-at]').datepicker({ format: 'yyyy-mm-dd' });
    $('[name=expense-range-days-end-at]').datepicker({ format: 'yyyy-mm-dd' });
})
</script>
@endsection
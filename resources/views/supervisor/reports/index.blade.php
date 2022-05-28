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
                <div class="card-header">ต้นทุน, ยอดขาย, กำไร</div>
                <div class="card-body text-center">
                    <div id="sales-bar-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">พลังงาน</div>
                <div class="card-body text-center">
                    <div id="energy-pie-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

var salesYearSummary = @json($salesYearSummary)

function salesChart(){
    var categories = salesYearSummary.monthYearTitles;

    var expenseData = [];
    for (const [key, value] of Object.entries(salesYearSummary.expenseYearSummary)) {
        expenseData.push(parseFloat(value.total_amount));
    }
    var expenses = {
        name: 'ต้นทุน',
        data: expenseData
    };

    var incomeData = [];
    for (const [key, value] of Object.entries(salesYearSummary.incomeYearSummary)) {
        incomeData.push(parseFloat(value.total_amount));
    }
    var incomes = {
        name: 'ยอดขาย',
        data: incomeData
    };

    var profitData = [];
    for (const [key, value] of Object.entries(salesYearSummary.profitYearSummary)) {
        profitData.push(parseFloat(value.total_amount));
    }
    var profits = {
        name: 'กำไร',
        data: profitData
    };

    var series = [incomes, expenses, profits];

    Highcharts.chart('sales-bar-chart', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'ต้นทุน, ยอดขาย, กำไร'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: categories,
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'จำนวนเงิน (บาท)'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} บาท</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: series
    });
}

function energyPieChart(){

    Highcharts.chart('energy-pie-chart', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'พลังงานที่ใช้'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            name: 'พลังงาน',
            colorByPoint: true,
            data: [{
                name: 'แก๊ส',
                y: 71.5,
                sliced: true,
                selected: true
            }, {
                name: 'น้ำ',
                y: 16.3
            }, {
                name: 'ไฟฟ้า',
                y: 12.2
            }]
        }]
    });
}

$(function(){
    salesChart();
    energyPieChart();
})
</script>
@endsection
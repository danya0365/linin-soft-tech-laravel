@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.employee') }}">พนักงาน</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.employee.select-employee') }}">พนักงาน: {{ $employee['name'] }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">สรุปข้อมูลของพนักงาน</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header text-center">สรุปข้อมูลของพนักงาน</div>
                <div class="card-body">
                    <form class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ route('worker.employee.employee-summary', ['employeeId' => $employee['id']]) }}" method="GET">
                        
                        <div class="col-12">
                            <div class="input-group">
                                <input type="date" name="date_start_at" value="{{ $dateStartAt }}" class="form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="date" name="date_end_at" value="{{ $dateEndAt }}" class="form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('worker.employee.employee-summary', ['employeeId' => $employee['id']]) }}" role="button" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>

                    <div class="row mb-3 text-center">
                        <div class="col-12">
                            <div class="bi bi-person-circle rounded-3 d-flex align-items-center justify-content-center p-3 py-6" style="font-size: 10em"></div>
                        </div>
                    </div>
                    <h5 class="card-title text-center">{{ $employee['name'] }}</h5>
                    <dl class="row">
                        <dt class="col-sm-3">Employee Code</dt>
                        <dd class="col-sm-9">{{ $employee['code'] }}</dd>
                    </dl>
                </div>
                <ul class="list-group list-group-flush">
                @if (isset($summaryReports['wash']))
                    @foreach ($summaryReports['wash'] as $summaryReport)
                    <li class="list-group-item">{{ $summaryReport['title'] }}: {{ number_format($summaryReport['value']) }} kg.</li>
                    @endforeach
                @endif
                @if (isset($summaryReports['dry']))
                    @foreach ($summaryReports['dry'] as $summaryReport)
                    <li class="list-group-item">{{ $summaryReport['title'] }}: {{ number_format($summaryReport['value']) }} kg.</li>
                    @endforeach
                @endif
                @if (isset($summaryReports['iron']))
                    @foreach ($summaryReports['iron'] as $summaryReport)
                    <li class="list-group-item">{{ $summaryReport['title'] }}: {{ number_format($summaryReport['value']) }} piece</li>
                    @endforeach
                @endif
                @if (isset($summaryReports['packing']))
                    @foreach ($summaryReports['packing'] as $summaryReport)
                    <li class="list-group-item">{{ $summaryReport['title'] }}: {{ number_format($summaryReport['value']) }} piece</li>
                    @endforeach
                @endif
                @if (isset($summaryReports['collect']))
                    @foreach ($summaryReports['collect'] as $summaryReport)
                    <li class="list-group-item">{{ $summaryReport['title'] }}: {{ number_format($summaryReport['value']) }} kg.</li>
                    @endforeach
                @endif
                @if (isset($summaryReports['collect_pack']))
                    @foreach ($summaryReports['collect_pack'] as $summaryReport)
                    <li class="list-group-item">{{ $summaryReport['title'] }}: {{ number_format($summaryReport['value']) }} pack</li>
                    @endforeach
                @endif
                </ul>
                <div class="card-body text-center">
                    <div id="employee-summary-scatter-plots-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

                    </div>
                </div>
                <div class="card-footer text-muted text-center">
                    เวลาการทำงานทั้งหมด: {{ $workingDuration }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function ($) {
    $.employeeSummaryScatterPlotsChart = function (options) {
        var settings = $.extend(
            {
                data: null,
                renderTo: null,
                tickInterval: 1 * 3600 * 1000, // 3 hours
                pointInterval: 3600 * 1000, // one day (in milisec.)
            },
            options
        );

        var dates = Object.keys(settings.data).map(function(key, item) { 
            var date = key.split('-');
            return {
                'min': Date.UTC(date[0], date[1], date[2], 0, 0, 0, 1), 
                'max': Date.UTC(date[0], date[1], date[2], 23, 59, 59)
            };
        });

        //console.log('dates', dates);
        var xAxis = dates.map(function(date, key) {
            var labelEnabled = key == 0 ? true : false;
            return {
                type: "datetime",
                tickInterval: settings.tickInterval,
                min: date.min,
                max: date.max,
                startOnTick: false,
                lineWidth: 0,
                minorGridLineWidth: 0,
                lineColor: "transparent",
                labels: {
                    enabled: labelEnabled,
                },
                minorTickLength: 0,
                tickLength: 0,
            };
        });

        //console.log('xAxis', xAxis);
        var colors = [
            "rgba(223, 83, 83, .5)", 
            "rgba(235, 146, 52, .5)", 
            "rgba(137, 235, 52, .5)", 
            "rgba(52, 235, 174, .5)",
            "rgba(52, 220, 235, .5)",
            "rgba(52, 128, 235, .5)",
            "rgba(162, 52, 235, .5)",
        ]
        var series = Object.keys(settings.data).map(function(key, index) {
            var color = colors[index];
            var date = key;
            var items = settings.data[key];
            var data = items.map(function(item, index) {
                var date = new Date(item.created_at);
                return [date.getTime(), item.value];
            });

            return {
                pointInterval: settings.pointInterval,
                name: date,
                color: color,
                xAxis: index,
                data: data,
                marker: {
                    symbol: "circle",
                    fillColor: color,
                    radius: 5,
                },
            };
        });

        console.log('series', series);

        var demoSeries = [
            {
                pointInterval: 3600 * 1000, // one day (in milisec.)
                name: "Sunday",
                color: "rgba(223, 83, 83, .5)",
                xAxis: 0,
                data: [
                    [Date.UTC(2013, 3, 7, 10), 70],
                    [Date.UTC(2013, 3, 7, 11, 30), 140],
                    [Date.UTC(2013, 3, 7, 12, 15), 110],
                    [Date.UTC(2013, 3, 7, 14, 45), 100],
                    [Date.UTC(2013, 3, 7, 16, 1), 160],
                ],
                marker: {
                    symbol: "circle",
                    fillColor: "rgba(223, 83, 83, .5)",
                    radius: 5,
                },
            },
            {
                pointInterval: 3600 * 1000, // one day (in milisec.)
                name: "Monday",
                color: "rgba(235, 146, 52, .5)",
                xAxis: 1,
                data: [
                    [Date.UTC(2013, 3, 6, 10, 9), 60],
                    [Date.UTC(2013, 3, 6, 11, 11), 110],
                    [Date.UTC(2013, 3, 6, 12, 21), 160],
                    [Date.UTC(2013, 3, 6, 14, 41), 120],
                    [Date.UTC(2013, 3, 6, 16), 90],
                ],
                marker: {
                    symbol: "circle",
                    fillColor: "rgba(235, 146, 52, .5)",
                    radius: 5,
                },
            },
            {
                pointInterval: 3600 * 1000, // one day (in milisec.)
                name: "Tuesday",
                color: "rgba(137, 235, 52, .5)",
                xAxis: 2,
                data: [
                    [Date.UTC(2013, 3, 5, 8, 11), 120],
                    [Date.UTC(2013, 3, 5, 9, 13), 130],
                    [Date.UTC(2013, 3, 5, 17, 31), 160],
                    [Date.UTC(2013, 3, 5, 18, 20), 130],
                ],
                marker: {
                    symbol: "circle",
                    fillColor: "rgba(137, 235, 52, .5)",
                    radius: 5,
                },
            },
            {
                pointInterval: 3600 * 1000, // one day (in milisec.)
                name: "Wednesday",
                color: "rgba(52, 235, 174, .5)",
                xAxis: 3,
                data: [
                    [Date.UTC(2013, 3, 4, 7, 16), 100],
                    [Date.UTC(2013, 3, 4, 9, 30), 120],
                    [Date.UTC(2013, 3, 4, 11, 34), 130],
                    [Date.UTC(2013, 3, 4, 17, 41), 160],
                    [Date.UTC(2013, 3, 4, 20, 53), 130],
                ],
                marker: {
                    symbol: "circle",
                    fillColor: "rgba(52, 235, 174, .5)",
                    radius: 5,
                },
            },
            {
                pointInterval: 3600 * 1000, // one day (in milisec.)
                name: "Thursday",
                color: "rgba(52, 220, 235, .5)",
                xAxis: 4,
                data: [
                    [Date.UTC(2013, 3, 3, 7, 16), 130],
                    [Date.UTC(2013, 3, 3, 9, 30), 100],
                    [Date.UTC(2013, 3, 3, 11, 34), 90],
                    [Date.UTC(2013, 3, 3, 17, 41), 160],
                    [Date.UTC(2013, 3, 3, 20, 53), 120],
                ],
                marker: {
                    symbol: "circle",
                    fillColor: "rgba(52, 220, 235, .5)",
                    radius: 5,
                },
            },
            {
                pointInterval: 3600 * 1000, // one day (in milisec.)
                name: "Friday",
                color: "rgba(52, 128, 235, .5)",
                xAxis: 5,
                data: [
                    [Date.UTC(2013, 3, 2, 7, 16), 80],
                    [Date.UTC(2013, 3, 2, 9, 30), 130],
                    [Date.UTC(2013, 3, 2, 11, 34), 150],
                    [Date.UTC(2013, 3, 2, 17, 41), 110],
                    [Date.UTC(2013, 3, 2, 20, 53), 90],
                ],
                marker: {
                    symbol: "circle",
                    fillColor: "rgba(52, 128, 235, .5)",
                    radius: 5,
                },
            },
            {
                pointInterval: 3600 * 1000, // one day (in milisec.)
                name: "Saturday",
                color: "rgba(162, 52, 235, .5)",
                xAxis: 6,
                data: [
                    [Date.UTC(2013, 3, 1, 7, 16), 96],
                    [Date.UTC(2013, 3, 1, 9, 30), 150],
                    [Date.UTC(2013, 3, 1, 11, 34), 100],
                    [Date.UTC(2013, 3, 1, 17, 41), 120],
                    [Date.UTC(2013, 3, 1, 20, 53), 140],
                ],
                marker: {
                    symbol: "circle",
                    fillColor: "rgba(162, 52, 235,.5)",
                    radius: 5,
                },
            },
        ];


        var demoXAxis = [
            {
                type: "datetime",
                tickInterval: 3 * 3600 * 1000, // 3 hours
                min: Date.UTC(2013, 3, 7, 0, 0, 0, 1),
                max: Date.UTC(2013, 3, 7, 23, 59, 59),
                startOnTick: false,
                lineWidth: 0,
                minorGridLineWidth: 0,
                lineColor: "transparent",
                labels: {
                    enabled: false,
                },
                minorTickLength: 0,
                tickLength: 0,
            },
            {
                type: "datetime",
                tickInterval: 3 * 3600 * 1000, // 3 hours
                min: Date.UTC(2013, 3, 6, 0, 0, 0, 1),
                max: Date.UTC(2013, 3, 6, 23, 59, 59),
                startOnTick: false,
                lineWidth: 0,
                minorGridLineWidth: 0,
                lineColor: "transparent",
                labels: {
                    enabled: false,
                },
                minorTickLength: 0,
                tickLength: 0,
            },
            {
                type: "datetime",
                tickInterval: 3 * 3600 * 1000, // 3 hours
                min: Date.UTC(2013, 3, 5, 0, 0, 0, 1),
                max: Date.UTC(2013, 3, 5, 23, 59, 59),
                startOnTick: false,
                lineWidth: 0,
                minorGridLineWidth: 0,
                lineColor: "transparent",
                labels: {
                    enabled: false,
                },
                minorTickLength: 0,
                tickLength: 0,
            },
            {
                type: "datetime",
                tickInterval: 3 * 3600 * 1000, // 3 hours
                min: Date.UTC(2013, 3, 4, 0, 0, 0, 1),
                max: Date.UTC(2013, 3, 4, 23, 59, 59),
                startOnTick: false,
                lineWidth: 0,
                minorGridLineWidth: 0,
                lineColor: "transparent",
                labels: {
                    enabled: false,
                },
                minorTickLength: 0,
                tickLength: 0,
            },
            {
                type: "datetime",
                tickInterval: 3 * 3600 * 1000, // 3 hours
                min: Date.UTC(2013, 3, 3, 0, 0, 0, 1),
                max: Date.UTC(2013, 3, 3, 23, 59, 59),
                startOnTick: false,
                lineWidth: 0,
                minorGridLineWidth: 0,
                lineColor: "transparent",
                labels: {
                    enabled: false,
                },
                minorTickLength: 0,
                tickLength: 0,
            },
            {
                type: "datetime",
                tickInterval: 3 * 3600 * 1000, // 3 hours
                min: Date.UTC(2013, 3, 2, 0, 0, 0, 1),
                max: Date.UTC(2013, 3, 2, 23, 59, 59),
                startOnTick: false,
                lineWidth: 0,
                minorGridLineWidth: 0,
                lineColor: "transparent",
                labels: {
                    enabled: false,
                },
                minorTickLength: 0,
                tickLength: 0,
            },
            {
                type: "datetime",
                tickInterval: 3 * 3600 * 1000, // 3 hours
                min: Date.UTC(2013, 3, 1, 0, 0, 0, 1),
                max: Date.UTC(2013, 3, 1, 23, 59, 59),
                startOnTick: false,
                lineWidth: 0,
                minorGridLineWidth: 0,
                lineColor: "transparent",
                labels: {
                    enabled: true,
                },
                minorTickLength: 0,
                tickLength: 0,
            },
        ];

        console.log('demoXAxis', demoXAxis);
        console.log('demoSeries', demoSeries);

        chart = new Highcharts.Chart({
            chart: {
                renderTo: settings.renderTo,
                type: "scatter",
                zoomType: "xy",
            },
            title: {
                text: "Performance per hour",
            },
            subtitle: {
                text: "last 7 days",
            },
            xAxis: demoXAxis,
            yAxis: {
                title: {
                    text: "จำนวนที่ทำได้ - Total",
                },
            },
            series: demoSeries,
        });
    };
})(jQuery);

var weekDayReports = @json($weekDayReports);
console.log('weekDayReports', weekDayReports);

$(function(){
    $.employeeSummaryScatterPlotsChart({
        renderTo: "employee-summary-scatter-plots-chart",
        data: weekDayReports
    });
})
</script>
@endsection
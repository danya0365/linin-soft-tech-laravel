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
                            <div class="rounded-3 d-flex align-items-center justify-content-center p-3 py-6">
                                <div style="max-width: 150px">
                                    <x-employee-avatar :photo="$employee['photo']" />
                                </div>
                            </div>
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
var weekDayReports = @json($weekDayReports);
$(function(){
    $.employeeSummaryScatterPlotsChart({
        renderTo: "employee-summary-scatter-plots-chart",
        data: weekDayReports
    });
})
</script>
@endsection
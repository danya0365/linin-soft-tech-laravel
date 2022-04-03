@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation-v1') }}">ปฏิบัติการ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation-v1.collect.select-job-group', ['jobGroupId' => $jobGroup['id']]) }}">ลูกค้า: {{ $jobGroup['customer']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation-v1.collect.select-job-group', ['jobGroupId' => $jobGroup['id']]) }}">น้ำหนักเปียก: {{ $jobGroup['wet_weight'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation-v1.collect.select-job-group', ['jobGroupId' => $jobGroup['id']]) }}">สถานะ: {{ $jobGroup['operation_status_text'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation-v1.collect.select-job-group', ['jobGroupId' => $jobGroup['id']]) }}">พนักงานพับแพ็ค: {{ $jobGroup['pick_up_employee']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation-v1.collect.select-job-group', ['jobGroupId' => $jobGroup['id']]) }}">จำนวน: {{ $jobGroup['total_pieces'] }} ชิ้น</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation-v1.collect.select-employee', ['jobGroupId' => $jobGroup['id']]) }}">พนักงานจัดเก็บ: {{ $jobGroup['collect_employee']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation-v1.collect.submit', ['jobGroupId' => $jobGroup['id']]) }}">น้ำหนักแห้ง: {{ $jobGroup['dry_weight'] }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">จัดเก็บ - สรุปยอดพนักงาน</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header text-center">สรุปยอดพนักงาน</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="d-grid gap-2" style="min-height: 60px">
                                <button class="btn btn-primary" type="submit">เพิ่ม</button>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="d-grid gap-2" style="min-height: 60px">
                                <button class="btn btn-danger" type="button">ลบ</button>
                            </div>
                        </div>
                        <div class="col-4">
                            <form method="POST" action="{{ route('worker.operation-v1.collect.employee-result', ['jobGroupId' => $jobGroup['id'] ]) }}"  role="form" enctype="multipart/form-data">
                                @csrf
                                {{ Form::hidden('operation_status', App\Enums\JobGroupStatus::Close()) }}
                                <div class="d-grid gap-2" style="min-height: 60px">
                                    <button class="btn btn-outline-secondary" type="submit">ปิดงาน</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3 text-center">
                        <div class="col-12">
                            <div class="bi bi-person-circle rounded-3 d-flex align-items-center justify-content-center p-3 py-6" style="font-size: 10em"></div>
                        </div>
                    </div>
                    <h5 class="card-title text-center">{{ $jobGroup['collect_employee']['name'] }}</h5>
                    <dl class="row">
                        <dt class="col-sm-3">Employee Code</dt>
                        <dd class="col-sm-9">{{ $jobGroup['collect_employee']['code'] }}</dd>
                    </dl>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($summaryReports as $summaryReport)
                    <li class="list-group-item">{{ $summaryReport['title'] }}: {{ number_format($summaryReport['value']) }} กก.</li>
                    @endforeach
                </ul>
                <div class="card-footer text-muted text-center">
                    เวลาการทำงานทั้งหมด: {{ $workingDuration }}
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {

    })
</script>

@endsection
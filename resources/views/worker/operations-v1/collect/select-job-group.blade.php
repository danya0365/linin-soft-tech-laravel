@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
          <li class="breadcrumb-item"><a href="{{ route('worker.operation-v1') }}">ปฏิบัติการ</a></li>
          <li class="breadcrumb-item active" aria-current="page">จัดเก็บ - เลือกรถเข็น</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        @foreach ($customers as $customer)
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">ลูกค้า: {{ $customer['name'] }}</div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach ($customer['job_groups'] as $jobGroup)
                        <div class="col-12">
                            <a href="{{ route('worker.operation-v1.collect.set-select-job-group', ['jobGroupId' => $jobGroup['id']]) }}" class="text-decoration-none">
                                <div class="card" style="width: 100%">
                                    <div class="card-header text-center">น้ำหนักเปียก: {{ $jobGroup['wet_weight'] }} กก., พนักงานไปรับ: {{ $jobGroup['pick_up_employee']['name'] }}, พนักงานพับแพ็ค: {{ $jobGroup['packing_employee']['name'] }}, สถานะ: {{ $jobGroup['operation_status_text'] }}</div>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($jobGroup['jobs'] as $job)
                                        <li class="list-group-item">น้ำหนักเปียก: {{ $job['wet_weight'] }}, จำนวน: {{ $job['piece'] }} ชิ้น, <span style="color: {{ $job['color'] }}">สี: {{ $job['color'] }}</span>, สถานะ: {{ $job['status_text'] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
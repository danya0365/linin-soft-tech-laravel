@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
          <li class="breadcrumb-item"><a href="{{ route('worker.operation') }}">ปฏิบัติการ</a></li>
          <li class="breadcrumb-item active" aria-current="page">อบ - เลือกรถเข็น</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        @foreach ($customers as $customer)
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">ลูกค้า: {{ $customer['name'] }}</div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach ($customer['jobs'] as $job)
                        <div class="col-12">
                            <a href="{{ route('worker.operation.dry.set-select-job', ['jobId' => $job['id']]) }}" class="text-decoration-none">
                                <div class="card" style="width: 100%">
                                    <ul class="list-group list-group-flush">
                                      <li class="list-group-item">น้ำหนักเปียก: {{ $job['wet_weight'] }}</li>
                                      <li class="list-group-item" style="color: {{ $job['color'] }}">สี: {{ $job['color'] }}</li>
                                      <li class="list-group-item">พนักงานซัก: {{ $job['wash_employee']['name'] }}</li>
                                      <li class="list-group-item">สถานะ: {{ $job['status_text'] }}</li>
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
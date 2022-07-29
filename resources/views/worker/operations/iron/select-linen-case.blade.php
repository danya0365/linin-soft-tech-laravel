@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation') }}">ปฏิบัติการ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.select-employee') }}">พนักงาน: {{ $operation['employee']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.select-customer', ['operationId' => $operation['id']]) }}">ลูกค้า: {{ $operation['customer']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.employee-summary', ['operationId' => $operation['id']]) }}">สรุปข้อมูลการรีด</a></li>
            <li class="breadcrumb-item active" aria-current="page">รีด - เลือกเคสงาน</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">เคสงาน</div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach ($operationLinenCases as $operationLinenCase )
                        <div class="col-sm-6">
                            <a href="{{ route('worker.operation.iron.set-select-linen-case', ['operationId' => $operation['id'], 'linenCase' => $operationLinenCase['var'], 'operationLinenProductId' => $operationLinenProduct->id]) }}">
                                <div class="p-3 border {{ $operationLinenCase['bg_css_class'] }}" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center">
                                        <div class="bi {{ $operationLinenCase['icon'] }} {{ $operationLinenCase['text_css_class'] }}" style="font-size: 3em"></div>
                                    </div>
                                    <div class="text-center {{ $operationLinenCase['text_css_class'] }}">{{ $operationLinenCase['name'] }}</div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
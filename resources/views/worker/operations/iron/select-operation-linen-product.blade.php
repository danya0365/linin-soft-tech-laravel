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
            <li class="breadcrumb-item active" aria-current="page">รีด - เลือกที่จะแก้ไขหรือลบ</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        @foreach ($operationLinenProducts as $operationLinenProduct)
        <div class="col-12 m-2">
            <div class="card" style="width: 100%">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">{{ $operationLinenProduct['linen_case'] ? $operationLinenProduct['linen_case']['name'] : 'ยังไม่ได้เลือก' }}</li>
                    <li class="list-group-item">ชนิดผ้า: {{ $operationLinenProduct['linen_product'] ? $operationLinenProduct['linen_product']['name'] : 'ยังไม่ได้เลือก' }}</li>
                    <li class="list-group-item">จำนวนชิ้น: {{ $operationLinenProduct['wet_weight'] ? $operationLinenProduct['wet_weight'] : 'ยังไม่ได้เลือก' }} ชิ้น</li>
                    <li class="list-group-item" style="color: {{ $operationLinenProduct['color'] ? $operationLinenProduct['color'] : '' }}">สี: {{ $operationLinenProduct['color'] ? $operationLinenProduct['color'] : 'ยังไม่ได้เลือก' }}</li>
                    <li class="list-group-item">
                        <div class="row g-2">
                            <div class="col-md-6 offset-md-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="d-grid gap-2">
                                            <a class="btn btn-primary" role="button" href="{{ route('worker.operation.iron.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}">แก้ไข</a>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-grid gap-2">
                                            <a class="btn btn-danger" role="button" href="{{ route('worker.operation.iron.delete-operation-linen-product', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}">ลบเลย</a>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
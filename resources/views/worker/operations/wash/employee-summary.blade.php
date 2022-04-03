@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation') }}">ปฏิบัติการ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.wash.select-employee') }}">พนักงาน: {{ $operation['employee']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.wash.select-customer', ['operationId' => $operation['id']]) }}">ลูกค้า: {{ $operation['customer']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.wash.select-washing-machine', ['operationId' => $operation['id']]) }}">{{ $operation['washing_machine']['name'] }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">ซัก - สรุปข้อมูลการซัก</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header text-center">สถานะการซัก</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="d-grid gap-2" style="min-height: 60px">
                                <button class="btn btn-primary" type="button" onclick="window.location='{{ route('worker.operation.wash.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => 0]) }}'">เพิ่ม</button>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="d-grid gap-2" style="min-height: 60px">
                                <button class="btn btn-danger" type="button" onclick="window.location='{{ route('worker.operation.wash.select-operation-linen-product', ['operationId' => $operation['id']]) }}'">แก้ไขหรือลบ</button>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="d-grid gap-2" style="min-height: 60px">
                                @if ( $operation == "close ")
                                <button class="btn btn-outline-secondary" type="button" onclick="window.location='{{ route('worker.operation.wash.set-close', ['operationId' => $operation['id']]) }}'">ปิดงาน</button>
                                @else 
                                <button class="btn btn-outline-secondary" type="button" onclick="window.location='{{ route('worker.operation.wash.set-in-progress', ['operationId' => $operation['id']]) }}'">เปิดใหม่</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">สถานะ: {{ $operation['status'] }}</li>
                    @foreach ($operationLinenProducts as $operationLinenProduct)
                    <li class="list-group-item">
                        {{ $operationLinenProduct['linen_case'] ? $operationLinenProduct['linen_case']['name'] : 'ยังไม่ได้เลือก' }},
                        ชนิดผ้า: {{ $operationLinenProduct['linen_product'] ? $operationLinenProduct['linen_product']['name'] : 'ยังไม่ได้เลือก' }},
                        น้ำหนักเปียก: {{ $operationLinenProduct['wet_weight'] ? $operationLinenProduct['wet_weight'] : 'ยังไม่ได้เลือก' }} กก.,
                        สี: <span style="color: {{ $operationLinenProduct['color'] ? $operationLinenProduct['color'] : '' }}">{{ $operationLinenProduct['color'] ? $operationLinenProduct['color'] : 'ยังไม่ได้เลือก' }}</span>
                    </li>
                    @endforeach
                </ul>
                <div class="card-footer text-muted text-center">
                    เวลาในการซักผ้า: {{ $operationTimeDuration }}
                </div>
              </div>
        </div>
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header text-center">สรุปข้อมูลการซักของพนักงาน</div>
                <div class="card-body">
                    <div class="row mb-3 text-center">
                        <div class="col-12">
                            <div class="bi bi-person-circle rounded-3 d-flex align-items-center justify-content-center p-3 py-6" style="font-size: 10em"></div>
                        </div>
                    </div>
                    <h5 class="card-title text-center">{{ $operation['wash_employee']['name'] }}</h5>
                    <dl class="row">
                        <dt class="col-sm-3">Employee Code</dt>
                        <dd class="col-sm-9">{{ $operation['wash_employee']['code'] }}</dd>
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
$(function(){
    
})
</script>

@endsection
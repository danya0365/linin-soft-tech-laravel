@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation') }}">ปฏิบัติการ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.select-employee') }}">พนักงาน: {{ $operation['employee']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.select-customer', ['operationId' => $operation['id']]) }}">ลูกค้า: {{ $operation['customer']['name'] }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">รีด - สรุปข้อมูลการรีด</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header text-center">สถานะการรีด</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="d-grid gap-2" style="min-height: 60px">
                                <button class="btn btn-primary" type="button" onclick="window.location='{{ route('worker.operation.iron.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => 0]) }}'">เพิ่ม</button>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="d-grid gap-2" style="min-height: 60px">
                                <button class="btn btn-danger" type="button" onclick="window.location='{{ route('worker.operation.iron.select-operation-linen-product', ['operationId' => $operation['id']]) }}'">แก้ไขหรือลบ</button>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="d-grid gap-2" style="min-height: 60px">
                                @if ( $operation['status'] != "close")
                                <button class="btn btn-outline-secondary" type="button" id="close-operation">ปิดงาน</button>
                                @else 
                                <button class="btn btn-outline-secondary" type="button" id="reopen-operation">เปิดใหม่</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">สถานะ: {{ $operation['status'] }}</li>
                    <li class="list-group-item">จำนวนที่รีดทั้งหมด: {{ $operation['total_iron_piece'] ? $operation['total_iron_piece'] : '-' }} ชิ้น</li>
                    @foreach ($operationLinenProducts as $operationLinenProduct)
                    <li class="list-group-item">
                        {{ $operationLinenProduct['linen_case'] ? $operationLinenProduct['linen_case']['name'] : 'ยังไม่ได้เลือก' }},
                        ชนิดผ้า: {{ $operationLinenProduct['linen_product'] ? $operationLinenProduct['linen_product']['name'] : 'ยังไม่ได้เลือก' }},
                        จำนวนที่รีด: {{ $operationLinenProduct['iron_piece'] ? $operationLinenProduct['iron_piece'] : 'ยังไม่ได้เลือก' }} ชิ้น,
                        สี: <span style="color: {{ $operationLinenProduct['color'] ? $operationLinenProduct['color'] : '' }}">{{ $operationLinenProduct['color'] ? $operationLinenProduct['color'] : 'ยังไม่ได้เลือก' }}</span>
                    </li>
                    @endforeach
                </ul>
                <div class="card-footer text-muted text-center">
                    เวลาในการรีดผ้า: {{ $operationTimeDuration }}
                </div>
              </div>
        </div>
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header text-center">สรุปข้อมูลการรีดของพนักงาน</div>
                <div class="card-body">
                    <div class="row mb-3 text-center">
                        <div class="col-12">
                            <div class="rounded-3 d-flex align-items-center justify-content-center p-3 py-6">
                                <div style="max-width: 150px">
                                    <x-employee-avatar :photo="$operation['iron_employee']['photo']" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5 class="card-title text-center">{{ $operation['iron_employee']['name'] }}</h5>
                    <dl class="row">
                        <dt class="col-sm-3">Employee Code</dt>
                        <dd class="col-sm-9">{{ $operation['iron_employee']['code'] }}</dd>
                    </dl>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($summaryReports as $summaryReport)
                    <li class="list-group-item">{{ $summaryReport['title'] }}: {{ number_format($summaryReport['value']) }} ชิ้น</li>
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
        var operationStatus = '{{ $operation['status'] }}';

        function askBeforeExit(e) {
            if(!e) e = window.event;
            //e.cancelBubble is supported by IE - this will kill the bubbling process.
            e.cancelBubble = true;
            e.returnValue = 'You sure you want to leave?'; //This is displayed on the dialog
    
            //e.stopPropagation works in Firefox.
            if (e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            }
        }

        function removeOnUnload(callback){
            window.onbeforeunload = null;
            window.pagehide = null;
            callback()
        }

        function setUpOnUnload(){
            window.onbeforeunload = operationStatus == 'close' ? null : askBeforeExit;
            window.pagehide = operationStatus == 'close' ? null : askBeforeExit;
        }

        function closeOperation(){
            removeOnUnload(function(){
                window.location='{{ route('worker.operation.iron.set-close', ['operationId' => $operation['id']]) }}'
            })
        }

        function reopenOperation(){
            removeOnUnload(function(){
                window.location='{{ route('worker.operation.iron.set-in-progress', ['operationId' => $operation['id']]) }}'
            })
        }

        setUpOnUnload();
        $("#close-operation").on("click", closeOperation);
        $("#reopen-operation").on("click", reopenOperation);
    })
</script>

@endsection
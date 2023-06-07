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
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.wash.employee-summary', ['operationId' => $operation['id']]) }}">สรุปข้อมูลการซัก</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.wash.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}">{{ $operationLinenProduct['linen_case']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.wash.select-linen-product', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}">{{ $operationLinenProduct['linen_product']['name'] }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">ซัก - แบบฟอร์ม Submit</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">น้ำหนักเปียกและสี</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('worker.operation.wash.set-select-weight-and-color', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}"  role="form" enctype="multipart/form-data">
                        @csrf
                        <div class="box box-info padding-1">
                            <div class="box-body">
                                
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <x-number-pad :inputName="'wet_weight'" :inputValue="$operationLinenProduct['wet_weight']">
                                            น้ำหนักกิโลกรัม
                                        </x-number-pad>
                                    </div>
                                    <div class="col-sm-6">
                                        <x-color-pad :inputName="'color'" :inputValue="$operationLinenProduct['color']">
                                            สี
                                        </x-color-pad>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label for="operation_date" class="form-label">วันที่บันทึก - Create Date</label>
                                        <input value="{{ $operationLinenProduct['operation_date'] }}" type="text" placeholder="YYYY-MM-DD" pattern="(?:19|20)(?:[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:29|30))|(?:(?:0[13578]|1[02])-31))|(?:[13579][26]|[02468][048])-02-29)"  name="operation_date" class="form-control" id="operation_date">
                                        @error('operation_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="operation_time" class="form-label">เวลาบันทึก - Create Time</label>
                                        <input value="{{ $operationLinenProduct['operation_time'] }}" type="text" placeholder="HH:MM" pattern="([01]?[0-9]{1}|2[0-3]{1}):[0-5]{1}[0-9]{1}" name="operation_time" class="form-control" id="operation_time">
                                        @error('operation_time')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                            <div class="box-footer">
                                <div class="row g-2 mt-2">
                                    <div class="col-6">
                                        <div class="d-grid gap-2" style="min-height: 60px">
                                            <button class="btn btn-primary" type="submit">ยืนยัน</button>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-grid gap-2" style="min-height: 60px">
                                            <button class="btn btn-danger" type="button" onclick="location.reload()">คืนค่า</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
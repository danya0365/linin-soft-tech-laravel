@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation') }}">ปฏิบัติการ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.deliver.select-employee') }}">พนักงาน  {{ $operation['employee']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.deliver.select-truck', ['operationId' => $operation['id']]) }}">{{ $operation['truck']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.deliver.employee-summary', ['operationId' => $operation['id']]) }}">สรุปข้อมูลการอบ</a></li>
            <li class="breadcrumb-item active" aria-current="page">ขนส่ง - เลือก Packing</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">รายการทั้งหมดที่รอการจัดส่ง</div>
                <div class="card-body">
                    <form class="form" method="POST" action="{{ route('worker.operation.deliver.select-collect-operation', ['operationId' => $operation['id']]) }}"  role="form" enctype="multipart/form-data">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>วันที่</th>
                                        <th>ประเภทงาน</th>
                                        <th>สินค้า</th>
                                        <th>สี</th>
                                        <th>ลูกค้า</th>
                                        <th>พนักงาน</th>
                                        <th>น้ำหนักที่จัดเก็บ (kg.)</th>
                                        <th>จำนวนที่จัดเก็บ (pack)</th>
                                        <th>เวลา</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($operations as $operation)
                                        <tr>
                                            <td>{{ $operation->created_at->format('Y-m-d') }}</td>
                                            <td>{{ App\Enums\OperationType::getDescription($operation->operation->operation_type) }}</td>
                                            <td>{{ $operation->linenProduct ? $operation->linenProduct->name : '-' }}</td>
                                            <td style="background-color: {{ $operation->color }}">{{ $operation->color }}</td>
                                            <td>{{ $operation->operation->customer->name }}</td>
                                            <td>{{ $operation->operation->employee->name }}</td>
                                            <td class="text-center">{{ $operation->collect_weight }}</td>
                                            <td class="text-center">{{ $operation->collect_pack }}</td>
                                            <td class="text-center">{{ $operation->created_at->format('H:i') }}</td>
                                            <td>
                                                <div class="d-grid gap-2">
                                                    <input data-id="{{ $operation->id }}" type="number" class="form-control input-value" value="" name="operationLinenProducts[{{ $operation->id }}]" autocomplete="off">
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="row g-2 m-2">
                                <div class="col-md-6 offset-md-3">
                                    <div class="row g-2 mt-2">
                                        <div class="col-6">
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-primary" type="submit">ยืนยันที่เลือก</button>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-danger" type="button">ยกเลิกที่เลือก</button>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    {!! $operations->withQueryString()->links() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var operations = @json($operations);
    $(function(){

        function validate(e){
            var selectCheckbox = [];
            $('.input-value').each(function() {
                if ($(this).val() != "") {
                    selectCheckbox.push({'id': $(this).data('id'), 'value': $(this).val()})
                }
            });

            var selectedOperations = selectCheckbox.map(function(element){
                return operations.data.find(operation => operation.id == element.id).linen_product.name
            })
    
            if (!selectCheckbox || selectCheckbox.length === 0) {
                Swal.fire('กรุณาเลือก 1 อย่าง', '', 'error')
                return false
            }
            return true;
        }
    
        $('.form').on('submit', function(e){
            return validate(e);
        })
    })
    </script>
@endsection
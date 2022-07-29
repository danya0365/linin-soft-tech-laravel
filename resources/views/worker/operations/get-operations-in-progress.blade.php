@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation') }}">Operation</a></li>
            <li class="breadcrumb-item active" aria-current="page">รายการทั้งหมด</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">รายการทั้งหมดที่ยังไม่ถูกปิดงาน - Operation In Progress</div>
                <div class="card-body">
 
                    <form class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ route('worker.operation.in-progress') }}" method="GET">
                        
                        <div class="col-12">
                            <div class="input-group">
                                <label class="input-group-text" for="linen-type">ชนิดผ้า</label>
                                <select class="form-select" id="linen-type" name="linenType" onchange="this.form.submit()">
                                    <option value="">ไม่เลือก</option>
                                    @foreach ( $linenTypes as $linenType )
                                    <option value="{{ $linenType->id }}"  {{ $linenTypeSelected == $linenType->id ? 'selected' : '' }}>{{ $linenType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group">
                                <label class="input-group-text" for="operation_type">ประเภทงาน</label>
                                <select class="form-select" id="operation_type" name="operation_type" onchange="this.form.submit()">
                                    <option value="">แสดงทั้งหมด - Show All</option>
                                    @foreach ( $operationTypes as $key => $operationType )
                                    <option value="{{ $key }}" {{ $operationTypeSelected == $key ? 'selected' : '' }}>{{ $operationType }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="input-group">
                                <input type="date" name="date_start_at" value="{{ $dateStartAt }}" class="form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="date" name="date_end_at" value="{{ $dateEndAt }}" class="form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group">
                                <label class="input-group-text" for="sort_order">เรียงโดย</label>
                                <select class="form-select" id="sort_order" name="sort_order" onchange="this.form.submit()">
                                    @foreach ( $sortOrders as $sortOrder )
                                    <option value="{{ $sortOrder['var'] }}" {{ $sortOrderSelected == $sortOrder['var'] ? 'selected' : '' }}>{{ $sortOrder['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('worker.operation.in-progress') }}" role="button" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>

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
                                    <th>จำนวนที่ซัก (kg.)</th>
                                    <th>จำนวนที่อบ (kg.)</th>
                                    <th>จำนวนที่รีด (piece)</th>
                                    <th>จำนวนที่พับแพ็ค (piece)</th>
                                    <th>น้ำหนักที่จัดเก็บ (kg.)</th>
                                    <th>จำนวนที่จัดเก็บ (pack)</th>
                                    <th>จำนวนที่ขนส่ง (pack)</th>
                                    <th>เวลา</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($operations as $operation)
                                    <tr>
                                        <td>{{ $operation->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('worker.operation.checkout-in-progress', $operation->operation->id) }}">
                                            {{ App\Enums\OperationType::getDescription($operation->operation->operation_type) }}
                                            </a>
                                        </td>
                                        <td>{{ $operation->linenProduct ? $operation->linenProduct->name : '-' }}</td>
                                        <td style="background-color: {{ $operation->color }}">{{ $operation->color }}</td>
                                        <td>{{ $operation->operation->customer ? $operation->operation->customer->name : '-' }}</td>
                                        <td>{{ $operation->operation->employee ? $operation->operation->employee->name : '-' }}</td>
                                        <td class="text-center">
                                            {{ $operation->wet_weight }}
                                            (#{{ $operation->operation->washing_machine_id ?? '-' }})
                                        </td>
                                        <td class="text-center">
                                            {{ $operation->dry_weight }}
                                            (#{{ $operation->operation->dryer_machine_id ?? '-' }})
                                        </td>
                                        <td class="text-center">{{ $operation->iron_piece }}</td>
                                        <td class="text-center">{{ $operation->packing_piece }}</td>
                                        <td class="text-center">{{ $operation->collect_weight }}</td>
                                        <td class="text-center">{{ $operation->collect_pack }}</td>
                                        <td class="text-center">
                                            {{ $operation->deliver_pack }}
                                            (#{{ $operation->operation->truck_id ?? '-' }})
                                        </td>
                                        <td class="text-center">{{ $operation->created_at->format('H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer">
                    {!! $operations->withQueryString()->links() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.customer') }}">ลูกค้า</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.customer.operation-summary') }}">{{ $customer['name'] }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">รายการทั้งหมด</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">รายการทั้งหมดของ {{ $customer['name'] }}</div>
                <div class="card-body">
                    <form class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ route('worker.customer.get-operations-by-customer', ['customerId' => $customer['id']]) }}" method="GET">
                        <div class="col-12">
                            <div class="input-group">
                                <input type="date" name="date_start_at" value="{{ $dateStartAt }}" class="form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="date" name="date_end_at" value="{{ $dateEndAt }}" class="form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
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
                                <label class="input-group-text" for="linen_product_id">สินค้า</label>
                                <select class="form-select" id="linen_product_id" name="linen_product_id" onchange="this.form.submit()">
                                    <option value="">แสดงทั้งหมด - Show All</option>
                                    @foreach ( $linenTypes as $linenType )
                                    <optgroup label="{{ $linenType['name'] }}">
                                        @foreach ( $linenType['linen_products'] as $linenProduct )
                                        <option value="{{ $linenProduct['id'] }}" {{ $linenProductSelected == $linenProduct['id'] ? 'selected' : '' }}>{{ $linenProduct['name'] }}</option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group">
                                <label class="input-group-text" for="linen_case">ชนิด</label>
                                <select class="form-select" id="linen_case" name="linen_case" onchange="this.form.submit()">
                                    <option value="">แสดงทั้งหมด - Show All</option>
                                    @foreach ( $linenCases as $key => $linenCase )
                                    <option value="{{ $linenCase['var'] }}" {{ $linenCaseSelected == $linenCase['var'] ? 'selected' : '' }}>{{ $linenCase['name'] }}</option>
                                    @endforeach
                                </select>
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
                            <a href="{{ route('worker.customer.get-operations-by-customer', ['customerId' => $customer['id']]) }}" role="button" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead">
                                <tr>
                                    <th>วันที่</th>
                                    <th>ประเภทงาน</th>
                                    <th>สินค้า</th>
                                    <th>ชนิด</th>
                                    <th>สี</th>
                                    <th>พนักงาน</th>
                                    <th>จำนวนที่ซัก</th>
                                    <th>จำนวนที่อบ</th>
                                    <th>จำนวนที่รีด</th>
                                    <th>จำนวนที่พับแพ็ค</th>
                                    <th>จำนวนที่จัดเก็บ</th>
                                    <th>เวลา</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($operations as $operation)
                                    <tr>
                                        <td>{{ $operation->created_at->format('Y-m-d') }}</td>
                                        <td>{{ App\Enums\OperationType::getDescription($operation->operation->operation_type) }}</td>
                                        <td>{{ $operation->linenProduct->name }}</td>
                                        <td>{{ $operation->linen_case }}</td>
                                        <td style="background-color: {{ $operation->color }}">{{ $operation->color }}</td>
                                        <td>{{ $operation->operation->employee->name }}</td>
                                        <td class="text-center">{{ $operation->wet_weight }}</td>
                                        <td class="text-center">{{ $operation->dry_weight }}</td>
                                        <td class="text-center">{{ $operation->iron_piece }}</td>
                                        <td class="text-center">{{ $operation->packing_piece }}</td>
                                        <td class="text-center">{{ $operation->collect_weight }}</td>
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
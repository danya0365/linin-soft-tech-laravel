@extends('layouts.user-customer')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user-customer') }}">User Customer</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user-customer.customer') }}">ลูกค้า</a></li>
            <li class="breadcrumb-item active" aria-current="page">รายการยอดรวม</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">รายการยอดรวมแต่ละลูกค้า</div>
                <div class="card-body">

                    <form class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ route('user-customer.customer.operation-summary') }}" method="GET">
                        
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
                            <a href="{{ route('user-customer.customer.operation-summary') }}" role="button" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead">
                                <tr>
                                    <th>ลูกค้า</th>
                                    <th>น้ำหนักผ้าเปียก</th>
                                    <th>น้ำหนักผ้าสะอาด</th>
                                    <th>น้ำหนักผ้าแก้ไข</th>
                                    <th>% ของเสีย</th>
                                    <th>น้ำหนักลูกค้า</th>
                                    <th>มากกว่าหรือน้อยกว่า</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($operations as $operation)
                                    <tr>
                                        <td>
                                            @if ( $operation->customer )
                                            <a href="{{ route('user-customer.customer.get-operations-by-customer', ['customerId' => $operation->customer->id]) }}">
                                                {{ $operation->customer->name }}
                                            </a>
                                            @else
                                            <i>ลูกค้าถูกลบ</i>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ number_format($operation->total_wet_weight) }}</td>
                                        <td class="text-center">{{ number_format($operation->total_collect_weight) }}</td>
                                        <td class="text-center">{{ number_format($operation->total_edit_collect_weight) }}</td>
                                        <td class="text-center">{{ number_format($operation->total_collect_weight > 0 ? $operation->total_edit_collect_weight*100/$operation->total_collect_weight : 100) }}%</td>
                                        <td class="text-center">{{ number_format($operation->total_billing_weight) }}</td>
                                        <td class="text-center">{{ number_format($operation->total_billing_weight-$operation->total_collect_weight) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>
                                        ยอดรวม
                                    </td>
                                    <td class="text-center">{{ number_format($summary->total_wet_weight) }}</td>
                                    <td class="text-center">{{ number_format($summary->total_collect_weight) }}</td>
                                    <td class="text-center">{{ number_format($summary->total_edit_collect_weight) }}</td>
                                    <td class="text-center">{{ number_format($summary->total_collect_weight > 0 ? $summary->total_edit_collect_weight*100/$summary->total_collect_weight : 100) }}%</td>
                                    <td class="text-center">{{ number_format($summary->total_billing_weight) }}</td>
                                    <td class="text-center">{{ number_format($summary->total_billing_weight-$summary->total_collect_weight) }}</td>
                                </tr>
                            </tfoot>
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
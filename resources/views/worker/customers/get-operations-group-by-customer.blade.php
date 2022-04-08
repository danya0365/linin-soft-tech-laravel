@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.customer') }}">ลูกค้า</a></li>
            <li class="breadcrumb-item active" aria-current="page">รายการยอดรวม</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">รายการยอดรวมแต่ละลูกค้า</div>
                <div class="card-body">

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
                                        <td>{{ $operation->operationCustomer->name }}</td>
                                        <td class="text-center">{{ number_format($operation->total_wet_weight) }}</td>
                                        <td class="text-center">{{ number_format($operation->total_collect_weight) }}</td>
                                        <td class="text-center">{{ number_format($operation->total_edit_collect_weight) }}</td>
                                        <td class="text-center">{{ number_format($operation->total_edit_collect_weight*100/$operation->total_collect_weight) }}%</td>
                                        <td class="text-center">{{ number_format($operation->total_billing_weight) }}</td>
                                        <td class="text-center">{{ number_format($operation->total_billing_weight-$operation->total_collect_weight) }}</td>
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
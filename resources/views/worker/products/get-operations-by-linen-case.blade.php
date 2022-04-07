@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.product') }}">สินค้า</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.product.select-linen-case') }}">{{ $linenCase['name'] }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">รายการทั้งหมด</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">รายการทั้งหมดของ {{ $linenCase['name'] }}</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead">
                                <tr>
                                    <th>No</th>
                                    
                                    <th>Operation Type</th>
                                    <th>Employee Id</th>
                                    <th>Customer Id</th>
                                    <th>Wash Employee Id</th>
                                    <th>Dry Employee Id</th>
                                    <th>Iron Employee Id</th>
                                    <th>Packing Employee Id</th>
                                    <th>Collect Employee Id</th>
                                    <th>Job Case</th>
                                    <th>Washing Machine Id</th>
                                    <th>Dryer Machine Id</th>
                                    <th>Total Wet Weight</th>
                                    <th>Total Dry Weight</th>
                                    <th>Total Iron Piece</th>
                                    <th>Total Packing Piece</th>
                                    <th>Colors</th>
                                    <th>Search Tags</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($operations as $operation)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        
                                        <td>{{ $operation->operation_type }}</td>
                                        <td>{{ $operation->employee_id }}</td>
                                        <td>{{ $operation->customer_id }}</td>
                                        <td>{{ $operation->wash_employee_id }}</td>
                                        <td>{{ $operation->dry_employee_id }}</td>
                                        <td>{{ $operation->iron_employee_id }}</td>
                                        <td>{{ $operation->packing_employee_id }}</td>
                                        <td>{{ $operation->collect_employee_id }}</td>
                                        <td>{{ $operation->job_case }}</td>
                                        <td>{{ $operation->washing_machine_id }}</td>
                                        <td>{{ $operation->dryer_machine_id }}</td>
                                        <td>{{ $operation->total_wet_weight }}</td>
                                        <td>{{ $operation->total_dry_weight }}</td>
                                        <td>{{ $operation->total_iron_piece }}</td>
                                        <td>{{ $operation->total_packing_piece }}</td>
                                        <td>{{ $operation->colors }}</td>
                                        <td>{{ $operation->search_tags }}</td>
                                        <td>{{ $operation->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {!! $operations->links() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
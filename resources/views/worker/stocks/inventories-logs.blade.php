@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.stock') }}">Stocks</a></li>
            <li class="breadcrumb-item active" aria-current="page">ประวัติสต๊อก - Stock history</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">
                    ประวัติสต๊อก - Stock history
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead">
                                <tr>
                                    <th>วันที่ทำรายการ - Entry Date</th>
                                    <th>Name</th>
                                    <th>Import/Export</th>
                                    <th>จำนวน - Count</th>
                                    <th>ค่าใช้จ่าย - Cost (Thai Baht)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventoryLogs as $inventoryLog)
                                    <tr>
                                        <td>{{ $inventoryLog->created_at->format('Y-m-d') }}</td>
                                        <td class="text-center">
                                            @if ($inventoryLog->inventory && $inventoryLog->inventory?->inventoryGroup)
                                                {{ $inventoryLog->inventory?->inventoryGroup?->name ?? '-' }}
                                                : {{ $inventoryLog->inventory?->name ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $inventoryLog->type }}</td>
                                        <td class="text-center">{{ $inventoryLog->quantity }}</td>
                                        <td class="text-center">{{ number_format($inventoryLog->cost) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer">
                    {!! $inventoryLogs->withQueryString()->links() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
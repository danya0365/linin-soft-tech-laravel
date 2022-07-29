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

                    <form class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ request()->url() }}" method="GET">

                        <div class="col-12">
                            <div class="input-group">
                                <label class="input-group-text" for="inventory_group_id">ประเภทสต๊อก</label>
                                <select class="form-select" id="inventory_group_id" name="inventory_group_id" onchange="this.form.submit()">
                                    <option value="">แสดงทั้งหมด - Show All</option>
                                    @foreach ( $inventoryGroups as $key => $inventoryGroup )
                                    <option value="{{ $inventoryGroup['id'] }}" {{ $inventoryGroupSelected == $inventoryGroup['id'] ? 'selected' : '' }}>{{ $inventoryGroup['name'] }}</option>
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
                            <a href="{{ route('worker.stock.inventories-log') }}" role="button" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success mb-2">
                            {{ $message }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead">
                                <tr>
                                    <th>วันที่ - Date</th>
                                    <th>Name</th>
                                    <th>Import/Export</th>
                                    <th>จำนวน - Count</th>
                                    <th>ค่าใช้จ่าย - Cost (Thai Baht)</th>
                                    <th></th>
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
                                        <td>
                                            <form class="delete-form" action="{{ route('worker.stock.inventory.delete', $inventoryLog->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i> Delete</button>
                                            </form>
                                        </td>
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
<script>
    $(function(){
        $('.delete-form').on('submit', function(e){
            if (!confirm("Are you sure?")) {
                return false;
            }
            return true
        })
    });
</script>
@endsection
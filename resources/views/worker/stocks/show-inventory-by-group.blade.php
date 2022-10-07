@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.stock') }}">Stocks</a></li>
            <li class="breadcrumb-item active" aria-current="page">รายการทั้งหมดของ {{ $inventoryGroup->name }} - Show Inventory by {{ $inventoryGroup->name }}</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">

            @if ($message = Session::get('success'))
            <div class="alert alert-success mb-2">
                {{ $message }}
            </div>
            @endif

            @if ($message = Session::get('error'))
            <div class="alert alert-danger mb-2">
                {{ $message }}
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">

                        <span id="card_title">
                            รายการทั้งหมดของ {{ $inventoryGroup->name }} - Show Inventory by {{ $inventoryGroup->name }}
                        </span>

                         <div class="float-right">
                            <a href="{{ route('worker.stock.create-inventory-by-group', ['inventoryGroupId' => $inventoryGroup->id]) }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                              {{ __('Create New') }}
                            </a>
                          </div>
                    </div>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead">
                                <tr>
                                    <th>วันที่อัพเดต</th>
                                    <th>สินค้า</th>
                                    <th>สต๊อกทั้งหมด</th>
                                    <th>สต๊อกคงเหลือ</th>
                                    <th>Unit</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventories as $inventory)
                                    <tr>
                                        <td>{{ $inventory->updated_at->format('Y-m-d') }}</td>
                                        <td class="text-center">{{ $inventory->name }}</td>
                                        <td class="text-center">{{ $inventory->total_quantity }}</td>
                                        <td class="text-center">{{ $inventory->remain_quantity }}</td>
                                        <td class="text-center">{{ $inventory->unit }}</td>
                                        <td class="text-center">

                                            <form class="delete-form" action="{{ route('worker.stock.delete-inventory', ['inventoryId' => $inventory->id]) }}" method="POST">
                                                @csrf

                                                <a class="btn btn-primary" href="{{ route('worker.stock.edit-inventory', ['inventoryId' => $inventory->id]) }}" role="button">
                                                    <i class="fa-solid fa-edit"></i>
                                                </a>
    
                                                <a class="btn btn-primary" href="{{ route('worker.stock.inventory.increase-stock', ['inventoryId' => $inventory->id]) }}" role="button">
                                                    <i class="fa-solid fa-plus"></i>
                                                </a>
                                                <a class="btn btn-danger" href="{{ route('worker.stock.inventory.decrease-stock', ['inventoryId' => $inventory->id]) }}" role="button">
                                                    <i class="fa-solid fa-minus"></i>
                                                </a>
                                                <a class="btn btn-info" href="{{ route('worker.stock.inventory.logs', ['inventoryId' => $inventory->id]) }}" role="button">
                                                    <i class="fa-solid fa-history"></i>
                                                </a>
    
                                                <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i></button>
                                                
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer">
                    {!! $inventories->withQueryString()->links() !!}
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
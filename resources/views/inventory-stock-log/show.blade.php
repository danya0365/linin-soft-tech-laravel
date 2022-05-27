@extends('layouts.app')

@section('template_title')
    {{ $inventoryStockLog->name ?? 'Show Inventory Stock Log' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Inventory Stock Log</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('inventory-stock-logs.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Employee Id:</strong>
                            {{ $inventoryStockLog->employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Inventory Id:</strong>
                            {{ $inventoryStockLog->inventory_id }}
                        </div>
                        <div class="form-group">
                            <strong>Type:</strong>
                            {{ $inventoryStockLog->type }}
                        </div>
                        <div class="form-group">
                            <strong>Quantity:</strong>
                            {{ $inventoryStockLog->quantity }}
                        </div>
                        <div class="form-group">
                            <strong>Cost:</strong>
                            {{ $inventoryStockLog->cost }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

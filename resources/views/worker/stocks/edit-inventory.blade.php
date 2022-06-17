@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.stock') }}">Stocks</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventory->inventoryGroup->id]) }}">รายการทั้งหมดของ {{ $inventory->inventoryGroup->name }} - Show Inventory by {{ $inventory->inventoryGroup->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">แก้ไข {{ $inventory->name }} - Edit {{ $inventory->name }}</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
            <form method="POST" action="{{ request()->url() }}"  role="form" enctype="multipart/form-data">
                @csrf

                <div class="card-header">
                    แก้ไข {{ $inventory->name }} - Edit {{ $inventory->name }}
                </div>
                <div class="card-body">
                    <div class="row row-cols-lg-auto g-3 align-items-center mb-2">
                        <div class="col-12">
                            <div class="input-group">
                                {{ Form::label('name', 'ชื่อ - Name', ['class' => "input-group-text"]) }}
                                {{ Form::text('name', $inventory->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name']) }}
                                {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group">
                                {{ Form::label('unit', 'Unit', ['class' => "input-group-text"]) }}
                                {{ Form::text('unit', $inventory->unit, ['class' => 'form-control' . ($errors->has('unit') ? ' is-invalid' : ''), 'placeholder' => 'Unit']) }}
                                {!! $errors->first('unit', '<div class="invalid-feedback">:message</div>') !!}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group">
                                {{ Form::label('total_quantity', 'จำนวนสต๊อกทั้งหมด - Total Quantity', ['class' => "input-group-text"]) }}
                                {{ Form::text('total_quantity', $inventory->total_quantity, ['class' => 'form-control' . ($errors->has('total_quantity') ? ' is-invalid' : ''), 'placeholder' => 'Total Quantity', 'readonly' => 'true']) }}
                                {!! $errors->first('total_quantity', '<div class="invalid-feedback">:message</div>') !!}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group">
                                {{ Form::label('remain_quantity', 'จำนวนสต๊อกคงเหลือ - Remain Quantity', ['class' => "input-group-text"]) }}
                                {{ Form::text('remain_quantity', $inventory->remain_quantity, ['class' => 'form-control' . ($errors->has('remain_quantity') ? ' is-invalid' : ''), 'placeholder' => 'Remain Quantity', 'readonly' => 'true']) }}
                                {!! $errors->first('remain_quantity', '<div class="invalid-feedback">:message</div>') !!}
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

@endsection
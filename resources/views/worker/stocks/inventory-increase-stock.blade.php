@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.stock') }}">Stocks</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventory->inventoryGroup->id]) }}">รายการทั้งหมดของ {{ $inventory->inventoryGroup->name }} - Show Inventory by {{ $inventory->inventoryGroup->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">เพิ่มสต๊อกของ {{ $inventory->name }} - Increase stock of {{ $inventory->name }}</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
            <form method="POST" action="{{ request()->url() }}"  role="form" enctype="multipart/form-data">
                @csrf

                <div class="card-header">
                    เพิ่มสต๊อกของ {{ $inventory->name }} - Increase stock of {{ $inventory->name }}
                </div>
                <div class="card-body">
                    <div class="row row-cols-lg-auto g-3 align-items-center mb-2">
                        <div class="col-12">
                            <div class="input-group">
                                {{ Form::label('name', 'ชื่อ - Name', ['class' => "input-group-text"]) }}
                                {{ Form::text('name', $inventory->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name', 'readonly' => 'true']) }}
                                {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group">
                                {{ Form::label('unit', 'Unit', ['class' => "input-group-text"]) }}
                                {{ Form::text('unit', $inventory->unit, ['class' => 'form-control' . ($errors->has('unit') ? ' is-invalid' : ''), 'placeholder' => 'Unit', 'readonly' => 'true']) }}
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
                        <div class="col-12">
                            <div class="input-group">
                                {{ Form::label('increase_quantity', 'จำนวนที่ต้องการเพิ่ม - Increase Count', ['class' => "input-group-text"]) }}
                                {{ Form::text('increase_quantity', 0, ['class' => 'form-control' . ($errors->has('increase_quantity') ? ' is-invalid' : ''), 'placeholder' => 'Increase Count']) }}
                                {!! $errors->first('increase_quantity', '<div class="invalid-feedback">:message</div>') !!}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group">
                                {{ Form::label('created_at', 'วันที่เพิ่มสต๊อก', ['class' => "input-group-text"]) }}
                                {{ Form::date('created_at', '', ['class' => 'form-control' . ($errors->has('created_at') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                                {!! $errors->first('created_at', '<div class="invalid-feedback">:message</div>') !!}
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
<script type="text/javascript">
    $(function(){
        var total_quantity = {{ $inventory->total_quantity }};
        var remain_quantity = {{ $inventory->remain_quantity }};
        $("#increase_quantity").on("keyup", function(){
            var increase_quantity = parseInt($(this).val());
            if (isNaN(increase_quantity)) {
                $("#total_quantity").val(total_quantity)
                $("#remain_quantity").val(remain_quantity)
                return;
            }
            $("#total_quantity").val(total_quantity + increase_quantity)
            $("#remain_quantity").val(remain_quantity + increase_quantity)
        });
    })
</script>

@endsection
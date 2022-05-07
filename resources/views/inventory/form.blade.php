<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('inventory_group_id') }}
            {{ Form::text('inventory_group_id', $inventory->inventory_group_id, ['class' => 'form-control' . ($errors->has('inventory_group_id') ? ' is-invalid' : ''), 'placeholder' => 'Inventory Group Id']) }}
            {!! $errors->first('inventory_group_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('name') }}
            {{ Form::text('name', $inventory->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name']) }}
            {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('unit') }}
            {{ Form::text('unit', $inventory->unit, ['class' => 'form-control' . ($errors->has('unit') ? ' is-invalid' : ''), 'placeholder' => 'Unit']) }}
            {!! $errors->first('unit', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_quantity') }}
            {{ Form::text('total_quantity', $inventory->total_quantity, ['class' => 'form-control' . ($errors->has('total_quantity') ? ' is-invalid' : ''), 'placeholder' => 'Total Quantity']) }}
            {!! $errors->first('total_quantity', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('remain_quantity') }}
            {{ Form::text('remain_quantity', $inventory->remain_quantity, ['class' => 'form-control' . ($errors->has('remain_quantity') ? ' is-invalid' : ''), 'placeholder' => 'Remain Quantity']) }}
            {!! $errors->first('remain_quantity', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
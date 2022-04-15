<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('energy_resource_id') }}
            {{ Form::text('energy_resource_id', $energyResourceLog->energy_resource_id, ['class' => 'form-control' . ($errors->has('energy_resource_id') ? ' is-invalid' : ''), 'placeholder' => 'Energy Resource Id']) }}
            {!! $errors->first('energy_resource_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('employee_id') }}
            {{ Form::text('employee_id', $energyResourceLog->employee_id, ['class' => 'form-control' . ($errors->has('employee_id') ? ' is-invalid' : ''), 'placeholder' => 'Employee Id']) }}
            {!! $errors->first('employee_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('value') }}
            {{ Form::text('value', $energyResourceLog->value, ['class' => 'form-control' . ($errors->has('value') ? ' is-invalid' : ''), 'placeholder' => 'Value']) }}
            {!! $errors->first('value', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('unit') }}
            {{ Form::text('unit', $energyResourceLog->unit, ['class' => 'form-control' . ($errors->has('unit') ? ' is-invalid' : ''), 'placeholder' => 'Unit']) }}
            {!! $errors->first('unit', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('lot_number') }}
            {{ Form::text('lot_number', $energyResourceLog->lot_number, ['class' => 'form-control' . ($errors->has('lot_number') ? ' is-invalid' : ''), 'placeholder' => 'Lot Number']) }}
            {!! $errors->first('lot_number', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
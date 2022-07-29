<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('department_id') }}
            {{ Form::text('department_id', $departmentDailyCostLog->department_id, ['class' => 'form-control' . ($errors->has('department_id') ? ' is-invalid' : ''), 'placeholder' => 'Department Id']) }}
            {!! $errors->first('department_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('daily_date') }}
            {{ Form::text('daily_date', $departmentDailyCostLog->daily_date, ['class' => 'form-control' . ($errors->has('daily_date') ? ' is-invalid' : ''), 'placeholder' => 'Daily Date']) }}
            {!! $errors->first('daily_date', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('cost') }}
            {{ Form::text('cost', $departmentDailyCostLog->cost, ['class' => 'form-control' . ($errors->has('cost') ? ' is-invalid' : ''), 'placeholder' => 'Cost']) }}
            {!! $errors->first('cost', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
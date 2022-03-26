<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('employee_id') }}
            {{ Form::text('employee_id', $employeeWorkingTime->employee_id, ['class' => 'form-control' . ($errors->has('employee_id') ? ' is-invalid' : ''), 'placeholder' => 'Employee Id']) }}
            {!! $errors->first('employee_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('working_date') }}
            {{ Form::text('working_date', $employeeWorkingTime->working_date, ['class' => 'form-control' . ($errors->has('working_date') ? ' is-invalid' : ''), 'placeholder' => 'Working Date']) }}
            {!! $errors->first('working_date', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('time_duration') }}
            {{ Form::text('time_duration', $employeeWorkingTime->time_duration, ['class' => 'form-control' . ($errors->has('time_duration') ? ' is-invalid' : ''), 'placeholder' => 'Time Duration']) }}
            {!! $errors->first('time_duration', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('job_id') }}
            {{ Form::text('job_id', $jobActivityLog->job_id, ['class' => 'form-control' . ($errors->has('job_id') ? ' is-invalid' : ''), 'placeholder' => 'Job Id']) }}
            {!! $errors->first('job_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('employee_id') }}
            {{ Form::text('employee_id', $jobActivityLog->employee_id, ['class' => 'form-control' . ($errors->has('employee_id') ? ' is-invalid' : ''), 'placeholder' => 'Employee Id']) }}
            {!! $errors->first('employee_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('log_type') }}
            {{ Form::text('log_type', $jobActivityLog->log_type, ['class' => 'form-control' . ($errors->has('log_type') ? ' is-invalid' : ''), 'placeholder' => 'Log Type']) }}
            {!! $errors->first('log_type', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('old_value') }}
            {{ Form::text('old_value', $jobActivityLog->old_value, ['class' => 'form-control' . ($errors->has('old_value') ? ' is-invalid' : ''), 'placeholder' => 'Old Value']) }}
            {!! $errors->first('old_value', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('new_value') }}
            {{ Form::text('new_value', $jobActivityLog->new_value, ['class' => 'form-control' . ($errors->has('new_value') ? ' is-invalid' : ''), 'placeholder' => 'New Value']) }}
            {!! $errors->first('new_value', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
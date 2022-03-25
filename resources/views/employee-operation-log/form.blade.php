<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('employee_id') }}
            {{ Form::text('employee_id', $employeeOperationLog->employee_id, ['class' => 'form-control' . ($errors->has('employee_id') ? ' is-invalid' : ''), 'placeholder' => 'Employee Id']) }}
            {!! $errors->first('employee_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('operation_type') }}
            {{ Form::text('operation_type', $employeeOperationLog->operation_type, ['class' => 'form-control' . ($errors->has('operation_type') ? ' is-invalid' : ''), 'placeholder' => 'Operation Type']) }}
            {!! $errors->first('operation_type', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('action_type') }}
            {{ Form::text('action_type', $employeeOperationLog->action_type, ['class' => 'form-control' . ($errors->has('action_type') ? ' is-invalid' : ''), 'placeholder' => 'Action Type']) }}
            {!! $errors->first('action_type', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('operation_id') }}
            {{ Form::text('operation_id', $operationLog->operation_id, ['class' => 'form-control' . ($errors->has('operation_id') ? ' is-invalid' : ''), 'placeholder' => 'Operation Id']) }}
            {!! $errors->first('operation_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('employee_id') }}
            {{ Form::text('employee_id', $operationLog->employee_id, ['class' => 'form-control' . ($errors->has('employee_id') ? ' is-invalid' : ''), 'placeholder' => 'Employee Id']) }}
            {!! $errors->first('employee_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('action_name') }}
            {{ Form::text('action_name', $operationLog->action_name, ['class' => 'form-control' . ($errors->has('action_name') ? ' is-invalid' : ''), 'placeholder' => 'Action Name']) }}
            {!! $errors->first('action_name', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('old_values') }}
            {{ Form::text('old_values', $operationLog->old_values, ['class' => 'form-control' . ($errors->has('old_values') ? ' is-invalid' : ''), 'placeholder' => 'Old Values']) }}
            {!! $errors->first('old_values', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('new_values') }}
            {{ Form::text('new_values', $operationLog->new_values, ['class' => 'form-control' . ($errors->has('new_values') ? ' is-invalid' : ''), 'placeholder' => 'New Values']) }}
            {!! $errors->first('new_values', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
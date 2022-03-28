<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('customer_id') }}
            {{ Form::text('customer_id', $jobGroup->customer_id, ['class' => 'form-control' . ($errors->has('customer_id') ? ' is-invalid' : ''), 'placeholder' => 'Customer Id']) }}
            {!! $errors->first('customer_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('employee_id') }}
            {{ Form::text('employee_id', $jobGroup->employee_id, ['class' => 'form-control' . ($errors->has('employee_id') ? ' is-invalid' : ''), 'placeholder' => 'Employee Id']) }}
            {!! $errors->first('employee_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('wet_weight') }}
            {{ Form::text('wet_weight', $jobGroup->wet_weight, ['class' => 'form-control' . ($errors->has('wet_weight') ? ' is-invalid' : ''), 'placeholder' => 'Wet Weight']) }}
            {!! $errors->first('wet_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('dry_weight') }}
            {{ Form::text('dry_weight', $jobGroup->dry_weight, ['class' => 'form-control' . ($errors->has('dry_weight') ? ' is-invalid' : ''), 'placeholder' => 'Dry Weight']) }}
            {!! $errors->first('dry_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_pieces') }}
            {{ Form::text('total_pieces', $jobGroup->total_pieces, ['class' => 'form-control' . ($errors->has('total_pieces') ? ' is-invalid' : ''), 'placeholder' => 'Total Pieces']) }}
            {!! $errors->first('total_pieces', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('operation_status') }}
            {{ Form::text('operation_status', $jobGroup->operation_status, ['class' => 'form-control' . ($errors->has('operation_status') ? ' is-invalid' : ''), 'placeholder' => 'Operation Status']) }}
            {!! $errors->first('operation_status', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt-4">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
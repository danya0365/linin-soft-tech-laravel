<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('job_group_id') }}
            {{ Form::text('job_group_id', $job->job_group_id, ['class' => 'form-control' . ($errors->has('job_group_id') ? ' is-invalid' : ''), 'placeholder' => 'Job Group Id']) }}
            {!! $errors->first('job_group_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('customer_id') }}
            {{ Form::text('customer_id', $job->customer_id, ['class' => 'form-control' . ($errors->has('customer_id') ? ' is-invalid' : ''), 'placeholder' => 'Customer Id']) }}
            {!! $errors->first('customer_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('employee_id') }}
            {{ Form::text('employee_id', $job->employee_id, ['class' => 'form-control' . ($errors->has('employee_id') ? ' is-invalid' : ''), 'placeholder' => 'Employee Id']) }}
            {!! $errors->first('employee_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('job_type') }}
            {{ Form::text('job_type', $job->job_type, ['class' => 'form-control' . ($errors->has('job_type') ? ' is-invalid' : ''), 'placeholder' => 'Job Type']) }}
            {!! $errors->first('job_type', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('washing_machine_id') }}
            {{ Form::text('washing_machine_id', $job->washing_machine_id, ['class' => 'form-control' . ($errors->has('washing_machine_id') ? ' is-invalid' : ''), 'placeholder' => 'Washing Machine Id']) }}
            {!! $errors->first('washing_machine_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('dryer_machine_id') }}
            {{ Form::text('dryer_machine_id', $job->dryer_machine_id, ['class' => 'form-control' . ($errors->has('dryer_machine_id') ? ' is-invalid' : ''), 'placeholder' => 'Dryer Machine Id']) }}
            {!! $errors->first('dryer_machine_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('linen_type_id') }}
            {{ Form::text('linen_type_id', $job->linen_type_id, ['class' => 'form-control' . ($errors->has('linen_type_id') ? ' is-invalid' : ''), 'placeholder' => 'Laundry Type Id']) }}
            {!! $errors->first('linen_type_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('wet_weight') }}
            {{ Form::text('wet_weight', $job->wet_weight, ['class' => 'form-control' . ($errors->has('wet_weight') ? ' is-invalid' : ''), 'placeholder' => 'Wet Weight']) }}
            {!! $errors->first('wet_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('color') }}
            {{ Form::text('color', $job->color, ['class' => 'form-control' . ($errors->has('color') ? ' is-invalid' : ''), 'placeholder' => 'Color']) }}
            {!! $errors->first('color', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('status') }}
            {{ Form::text('status', $job->status, ['class' => 'form-control' . ($errors->has('status') ? ' is-invalid' : ''), 'placeholder' => 'Status']) }}
            {!! $errors->first('status', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('tags') }}
            {{ Form::text('tags', $job->tags, ['class' => 'form-control' . ($errors->has('tags') ? ' is-invalid' : ''), 'placeholder' => 'Tags']) }}
            {!! $errors->first('tags', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
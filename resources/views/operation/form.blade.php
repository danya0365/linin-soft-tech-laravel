<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('operation_type') }}
            {{ Form::text('operation_type', $operation->operation_type, ['class' => 'form-control' . ($errors->has('operation_type') ? ' is-invalid' : ''), 'placeholder' => 'Operation Type']) }}
            {!! $errors->first('operation_type', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('employee_id') }}
            {{ Form::text('employee_id', $operation->employee_id, ['class' => 'form-control' . ($errors->has('employee_id') ? ' is-invalid' : ''), 'placeholder' => 'Employee Id']) }}
            {!! $errors->first('employee_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('customer_id') }}
            {{ Form::text('customer_id', $operation->customer_id, ['class' => 'form-control' . ($errors->has('customer_id') ? ' is-invalid' : ''), 'placeholder' => 'Customer Id']) }}
            {!! $errors->first('customer_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('wash_employee_id') }}
            {{ Form::text('wash_employee_id', $operation->wash_employee_id, ['class' => 'form-control' . ($errors->has('wash_employee_id') ? ' is-invalid' : ''), 'placeholder' => 'Wash Employee Id']) }}
            {!! $errors->first('wash_employee_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('dry_employee_id') }}
            {{ Form::text('dry_employee_id', $operation->dry_employee_id, ['class' => 'form-control' . ($errors->has('dry_employee_id') ? ' is-invalid' : ''), 'placeholder' => 'Dry Employee Id']) }}
            {!! $errors->first('dry_employee_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('iron_employee_id') }}
            {{ Form::text('iron_employee_id', $operation->iron_employee_id, ['class' => 'form-control' . ($errors->has('iron_employee_id') ? ' is-invalid' : ''), 'placeholder' => 'Iron Employee Id']) }}
            {!! $errors->first('iron_employee_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('packing_employee_id') }}
            {{ Form::text('packing_employee_id', $operation->packing_employee_id, ['class' => 'form-control' . ($errors->has('packing_employee_id') ? ' is-invalid' : ''), 'placeholder' => 'Packing Employee Id']) }}
            {!! $errors->first('packing_employee_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('collect_employee_id') }}
            {{ Form::text('collect_employee_id', $operation->collect_employee_id, ['class' => 'form-control' . ($errors->has('collect_employee_id') ? ' is-invalid' : ''), 'placeholder' => 'Collect Employee Id']) }}
            {!! $errors->first('collect_employee_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('job_case') }}
            {{ Form::text('job_case', $operation->job_case, ['class' => 'form-control' . ($errors->has('job_case') ? ' is-invalid' : ''), 'placeholder' => 'Job Case']) }}
            {!! $errors->first('job_case', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('washing_machine_id') }}
            {{ Form::text('washing_machine_id', $operation->washing_machine_id, ['class' => 'form-control' . ($errors->has('washing_machine_id') ? ' is-invalid' : ''), 'placeholder' => 'Washing Machine Id']) }}
            {!! $errors->first('washing_machine_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('dryer_machine_id') }}
            {{ Form::text('dryer_machine_id', $operation->dryer_machine_id, ['class' => 'form-control' . ($errors->has('dryer_machine_id') ? ' is-invalid' : ''), 'placeholder' => 'Dryer Machine Id']) }}
            {!! $errors->first('dryer_machine_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_wet_weight') }}
            {{ Form::text('total_wet_weight', $operation->total_wet_weight, ['class' => 'form-control' . ($errors->has('total_wet_weight') ? ' is-invalid' : ''), 'placeholder' => 'Total Wet Weight']) }}
            {!! $errors->first('total_wet_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_dry_weight') }}
            {{ Form::text('total_dry_weight', $operation->total_dry_weight, ['class' => 'form-control' . ($errors->has('total_dry_weight') ? ' is-invalid' : ''), 'placeholder' => 'Total Dry Weight']) }}
            {!! $errors->first('total_dry_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_iron_piece') }}
            {{ Form::text('total_iron_piece', $operation->total_iron_piece, ['class' => 'form-control' . ($errors->has('total_iron_piece') ? ' is-invalid' : ''), 'placeholder' => 'Total Iron Piece']) }}
            {!! $errors->first('total_iron_piece', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_packing_piece') }}
            {{ Form::text('total_packing_piece', $operation->total_packing_piece, ['class' => 'form-control' . ($errors->has('total_packing_piece') ? ' is-invalid' : ''), 'placeholder' => 'Total Packing Piece']) }}
            {!! $errors->first('total_packing_piece', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('colors') }}
            {{ Form::text('colors', $operation->colors, ['class' => 'form-control' . ($errors->has('colors') ? ' is-invalid' : ''), 'placeholder' => 'Colors']) }}
            {!! $errors->first('colors', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('search_tags') }}
            {{ Form::text('search_tags', $operation->search_tags, ['class' => 'form-control' . ($errors->has('search_tags') ? ' is-invalid' : ''), 'placeholder' => 'Search Tags']) }}
            {!! $errors->first('search_tags', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('status') }}
            {{ Form::text('status', $operation->status, ['class' => 'form-control' . ($errors->has('status') ? ' is-invalid' : ''), 'placeholder' => 'Status']) }}
            {!! $errors->first('status', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
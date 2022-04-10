<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('customer_id') }}
            {{ Form::text('customer_id', $customerOperationDailySummary->customer_id, ['class' => 'form-control' . ($errors->has('customer_id') ? ' is-invalid' : ''), 'placeholder' => 'Customer Id']) }}
            {!! $errors->first('customer_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('operation_date') }}
            {{ Form::text('operation_date', $customerOperationDailySummary->operation_date, ['class' => 'form-control' . ($errors->has('operation_date') ? ' is-invalid' : ''), 'placeholder' => 'Operation Date']) }}
            {!! $errors->first('operation_date', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_wet_weight') }}
            {{ Form::text('total_wet_weight', $customerOperationDailySummary->total_wet_weight, ['class' => 'form-control' . ($errors->has('total_wet_weight') ? ' is-invalid' : ''), 'placeholder' => 'Total Wet Weight']) }}
            {!! $errors->first('total_wet_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_dry_weight') }}
            {{ Form::text('total_dry_weight', $customerOperationDailySummary->total_dry_weight, ['class' => 'form-control' . ($errors->has('total_dry_weight') ? ' is-invalid' : ''), 'placeholder' => 'Total Dry Weight']) }}
            {!! $errors->first('total_dry_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_iron_piece') }}
            {{ Form::text('total_iron_piece', $customerOperationDailySummary->total_iron_piece, ['class' => 'form-control' . ($errors->has('total_iron_piece') ? ' is-invalid' : ''), 'placeholder' => 'Total Iron Piece']) }}
            {!! $errors->first('total_iron_piece', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_packing_piece') }}
            {{ Form::text('total_packing_piece', $customerOperationDailySummary->total_packing_piece, ['class' => 'form-control' . ($errors->has('total_packing_piece') ? ' is-invalid' : ''), 'placeholder' => 'Total Packing Piece']) }}
            {!! $errors->first('total_packing_piece', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_edit_collect_weight') }}
            {{ Form::text('total_edit_collect_weight', $customerOperationDailySummary->total_edit_collect_weight, ['class' => 'form-control' . ($errors->has('total_edit_collect_weight') ? ' is-invalid' : ''), 'placeholder' => 'Total Edit Collect Weight']) }}
            {!! $errors->first('total_edit_collect_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_collect_weight') }}
            {{ Form::text('total_collect_weight', $customerOperationDailySummary->total_collect_weight, ['class' => 'form-control' . ($errors->has('total_collect_weight') ? ' is-invalid' : ''), 'placeholder' => 'Total Collect Weight']) }}
            {!! $errors->first('total_collect_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_billing_weight') }}
            {{ Form::text('total_billing_weight', $customerOperationDailySummary->total_billing_weight, ['class' => 'form-control' . ($errors->has('total_billing_weight') ? ' is-invalid' : ''), 'placeholder' => 'Total Billing Weight']) }}
            {!! $errors->first('total_billing_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('name') }}
            {{ Form::text('name', $customer->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name']) }}
            {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('customer_group_id') }}
            {{ Form::text('customer_group_id', $customer->customer_group_id, ['class' => 'form-control' . ($errors->has('customer_group_id') ? ' is-invalid' : ''), 'placeholder' => 'Customer Group Id']) }}
            {!! $errors->first('customer_group_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_wet_weight') }}
            {{ Form::text('total_wet_weight', $customer->total_wet_weight, ['class' => 'form-control' . ($errors->has('total_wet_weight') ? ' is-invalid' : ''), 'placeholder' => 'Total Wet Weight']) }}
            {!! $errors->first('total_wet_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_dry_weight') }}
            {{ Form::text('total_dry_weight', $customer->total_dry_weight, ['class' => 'form-control' . ($errors->has('total_dry_weight') ? ' is-invalid' : ''), 'placeholder' => 'Total Dry Weight']) }}
            {!! $errors->first('total_dry_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_billing_weight') }}
            {{ Form::text('total_billing_weight', $customer->total_billing_weight, ['class' => 'form-control' . ($errors->has('total_billing_weight') ? ' is-invalid' : ''), 'placeholder' => 'Total Billing Weight']) }}
            {!! $errors->first('total_billing_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_edit_weight') }}
            {{ Form::text('total_edit_weight', $customer->total_edit_weight, ['class' => 'form-control' . ($errors->has('total_edit_weight') ? ' is-invalid' : ''), 'placeholder' => 'Total Edit Weight']) }}
            {!! $errors->first('total_edit_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('total_billing_payment') }}
            {{ Form::text('total_billing_payment', $customer->total_billing_payment, ['class' => 'form-control' . ($errors->has('total_billing_payment') ? ' is-invalid' : ''), 'placeholder' => 'Total Billing Payment']) }}
            {!! $errors->first('total_billing_payment', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt-4">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
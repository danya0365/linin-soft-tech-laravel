<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('type_name') }}
            {{ Form::text('type_name', $income->type_name, ['class' => 'form-control' . ($errors->has('type_name') ? ' is-invalid' : ''), 'placeholder' => 'Type Name']) }}
            {!! $errors->first('type_name', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('table_name') }}
            {{ Form::text('table_name', $income->table_name, ['class' => 'form-control' . ($errors->has('table_name') ? ' is-invalid' : ''), 'placeholder' => 'Table Name']) }}
            {!! $errors->first('table_name', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('table_id') }}
            {{ Form::text('table_id', $income->table_id, ['class' => 'form-control' . ($errors->has('table_id') ? ' is-invalid' : ''), 'placeholder' => 'Table Id']) }}
            {!! $errors->first('table_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('amount') }}
            {{ Form::text('amount', $income->amount, ['class' => 'form-control' . ($errors->has('amount') ? ' is-invalid' : ''), 'placeholder' => 'Amount']) }}
            {!! $errors->first('amount', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
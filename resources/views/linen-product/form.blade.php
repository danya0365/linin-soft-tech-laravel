<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('linen_type_id') }}
            {{ Form::text('linen_type_id', $linenProduct->linen_type_id, ['class' => 'form-control' . ($errors->has('linen_type_id') ? ' is-invalid' : ''), 'placeholder' => 'Linen Type Id']) }}
            {!! $errors->first('linen_type_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('name') }}
            {{ Form::text('name', $linenProduct->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name']) }}
            {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt-4">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
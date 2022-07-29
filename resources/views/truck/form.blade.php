<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('name') }}
            {{ Form::text('name', $truck->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name']) }}
            {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('photo') }}
            {{ Form::text('photo', $truck->photo, ['class' => 'form-control' . ($errors->has('photo') ? ' is-invalid' : ''), 'placeholder' => 'Photo']) }}
            {!! $errors->first('photo', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('plate_number') }}
            {{ Form::text('plate_number', $truck->plate_number, ['class' => 'form-control' . ($errors->has('plate_number') ? ' is-invalid' : ''), 'placeholder' => 'Plate Number']) }}
            {!! $errors->first('plate_number', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('operation_id') }}
            {{ Form::text('operation_id', $truck->operation_id, ['class' => 'form-control' . ($errors->has('operation_id') ? ' is-invalid' : ''), 'placeholder' => 'Operation Id']) }}
            {!! $errors->first('operation_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
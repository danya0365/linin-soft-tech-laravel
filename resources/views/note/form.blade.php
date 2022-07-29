<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('message') }}
            {{ Form::text('message', $note->message, ['class' => 'form-control' . ($errors->has('message') ? ' is-invalid' : ''), 'placeholder' => 'Message']) }}
            {!! $errors->first('message', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('image_url') }}
            {{ Form::text('image_url', $note->image_url, ['class' => 'form-control' . ($errors->has('image_url') ? ' is-invalid' : ''), 'placeholder' => 'Image Url']) }}
            {!! $errors->first('image_url', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('cost') }}
            {{ Form::text('cost', $note->cost, ['class' => 'form-control' . ($errors->has('cost') ? ' is-invalid' : ''), 'placeholder' => 'Cost']) }}
            {!! $errors->first('cost', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('washing_machine_id') }}
            {{ Form::text('washing_machine_id', $note->washing_machine_id, ['class' => 'form-control' . ($errors->has('washing_machine_id') ? ' is-invalid' : ''), 'placeholder' => 'Washing Machine Id']) }}
            {!! $errors->first('washing_machine_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('dryer_machine_id') }}
            {{ Form::text('dryer_machine_id', $note->dryer_machine_id, ['class' => 'form-control' . ($errors->has('dryer_machine_id') ? ' is-invalid' : ''), 'placeholder' => 'Dryer Machine Id']) }}
            {!! $errors->first('dryer_machine_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('truck_id') }}
            {{ Form::text('truck_id', $note->truck_id, ['class' => 'form-control' . ($errors->has('truck_id') ? ' is-invalid' : ''), 'placeholder' => 'Truck Id']) }}
            {!! $errors->first('truck_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
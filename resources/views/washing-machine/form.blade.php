<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('name') }}
            {{ Form::text('name', $washingMachine->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name']) }}
            {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('photo') }}
            {{ Form::text('photo', $washingMachine->photo, ['class' => 'form-control' . ($errors->has('photo') ? ' is-invalid' : ''), 'placeholder' => 'Photo']) }}
            {!! $errors->first('photo', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('maximum_weight') }}
            {{ Form::text('maximum_weight', $washingMachine->maximum_weight, ['class' => 'form-control' . ($errors->has('maximum_weight') ? ' is-invalid' : ''), 'placeholder' => 'Maximum Weight']) }}
            {!! $errors->first('maximum_weight', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('service_status', 'สถานะเครื่อง') }}
            {{ Form::select('service_status', [
                'available' => '✅ พร้อมใช้งาน',
                'broken' => '🔧 เสีย/ซ่อม'
            ], $washingMachine->service_status ?? 'available', ['class' => 'form-control' . ($errors->has('service_status') ? ' is-invalid' : '')]) }}
            {!! $errors->first('service_status', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt-4">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('linen_type_id') }}
            <select name="linen_type_id" class="form-select">
                <option value=""></option>
            @foreach (App\Models\LinenType::get() as $linenType)
                <option value="{{ $linenType->id }}" @selected($linenProduct->linen_type_id == $linenType->id)>
                    {{ $linenType->name }}
                </option>
            @endforeach
            </select>
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
<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('name') }}
            {{ Form::text('name', $customer->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name']) }}
            {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('customer_group_id') }}
            <select name="customer_group_id" class="form-select">
                <option value=""></option>
            @foreach (App\Models\CustomerGroup::get() as $customerGroup)
                <option value="{{ $customerGroup->id }}" @selected($customer->customer_group_id == $customerGroup->id)>
                    {{ $customerGroup->name }}
                </option>
            @endforeach
            </select>
            {!! $errors->first('customer_group_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt-4">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
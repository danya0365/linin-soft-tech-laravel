<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('name') }}
            {{ Form::text('name', $user->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name']) }}
            {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('email') }}
            {{ Form::text('email', $user->email, ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'placeholder' => 'Email']) }}
            {!! $errors->first('email', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('role') }}
            <select class="form-select" id="role" name="role">
                <option value="">เลือก</option>
                @foreach ( $userRoles as $key => $userRole )
                <option value="{{ $key }}" {{ $user->role == $key ? 'selected' : '' }}>{{ $userRole }}</option>
                @endforeach
            </select>
            {!! $errors->first('role', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        

    </div>
    <div class="box-footer mt-4">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
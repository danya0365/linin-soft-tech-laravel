<div class="col-12">
    {{ Form::label('name', 'Name', ['class' => 'form-label']) }}
    {{ Form::text('name', $user->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name']) }}
    {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
</div>
<div class="col-12">
    {{ Form::label('email', 'Email', ['class' => 'form-label']) }}
    {{ Form::text('email', $user->email, ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'placeholder' => 'Email']) }}
    {!! $errors->first('email', '<div class="invalid-feedback">:message</div>') !!}
</div>
<div class="col-12">
    {{ Form::label('role', 'Role', ['class' => 'form-label']) }}
    <select class="form-select" id="role" name="role">
        <option value="">เลือก</option>
        @foreach ( $userRoles as $key => $userRole )
        <option value="{{ $key }}" {{ $user->role == $key ? 'selected' : '' }}>{{ $userRole }}</option>
        @endforeach
    </select>
    {!! $errors->first('role', '<div class="invalid-feedback">:message</div>') !!}
</div>

<div class="col-12">
    <button type="submit" class="btn btn-primary">Submit</button>
    <button type="reset" class="btn btn-outline-secondary">Reset</button>
</div>
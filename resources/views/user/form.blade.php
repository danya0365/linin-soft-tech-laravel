<div class="col-12">
    {{ Form::label('name', 'Name', ['class' => 'form-label']) }}
    {{ Form::text('name', $user->name, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Name']) }}
    {!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
</div>
<div class="col-12">
    {{ Form::label('password', 'Password', ['class' => 'form-label']) }}
    {{ Form::text('password', '', ['class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'placeholder' => 'Password']) }}
    {!! $errors->first('password', '<div class="invalid-feedback">:message</div>') !!}
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
    {{ Form::label('customer_account', 'Customer ID', ['class' => 'form-label']) }}
    <select class="form-select" id="customer_account" name="customer_account">
        <option value="">เลือก</option>
        @foreach ( $customerAccounts as $key => $customerAccount )
        <option value="{{ $customerAccount->id }}" {{ $user->customer_account == $customerAccount->id ? 'selected' : '' }}>{{ $customerAccount->name }}</option>
        @endforeach
    </select>
    {!! $errors->first('customer_account', '<div class="invalid-feedback">:message</div>') !!}
</div>

<div class="col-12">
    {{ Form::label('is_can_access_admin', 'Access Admin', ['class' => 'form-label']) }}
    <div class="form-group">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="is_can_access_admin_1" name="is_can_access_admin" value="1" {{ $user->is_can_access_admin == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="is_can_access_admin_1">Yes</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="is_can_access_admin_0" name="is_can_access_admin" value="0" {{ $user->is_can_access_admin == '0' ? 'checked' : '' }}>
            <label class="form-check-label" for="is_can_access_admin_0">No</label>
        </div>
    </div>
    {!! $errors->first('is_can_access_admin', '<div class="invalid-feedback">:message</div>') !!}
</div>

<div class="col-12">
    {{ Form::label('is_can_access_manager', 'Access Manager', ['class' => 'form-label']) }}
    <div class="form-group">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="is_can_access_manager_1" name="is_can_access_manager" value="1" {{ $user->is_can_access_manager == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="is_can_access_manager_1">Yes</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="is_can_access_manager_0" name="is_can_access_manager" value="0" {{ $user->is_can_access_manager == '0' ? 'checked' : '' }}>
            <label class="form-check-label" for="is_can_access_manager_0">No</label>
        </div>
    </div>
    {!! $errors->first('is_can_access_manager', '<div class="invalid-feedback">:message</div>') !!}
</div>

<div class="col-12">
    {{ Form::label('is_can_access_supervisor', 'Access Supervisor', ['class' => 'form-label']) }}
    <div class="form-group">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="is_can_access_supervisor_1" name="is_can_access_supervisor" value="1" {{ $user->is_can_access_supervisor == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="is_can_access_supervisor_1">Yes</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="is_can_access_supervisor_0" name="is_can_access_supervisor" value="0" {{ $user->is_can_access_supervisor == '0' ? 'checked' : '' }}>
            <label class="form-check-label" for="is_can_access_supervisor_0">No</label>
        </div>
    </div>
    {!! $errors->first('is_can_access_supervisor', '<div class="invalid-feedback">:message</div>') !!}
</div>

<div class="col-12">
    {{ Form::label('is_can_access_customer', 'Access Customer', ['class' => 'form-label']) }}
    <div class="form-group">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="is_can_access_customer_1" name="is_can_access_customer" value="1" {{ $user->is_can_access_customer == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="is_can_access_customer_1">Yes</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="is_can_access_customer_0" name="is_can_access_customer" value="0" {{ $user->is_can_access_customer == '0' ? 'checked' : '' }}>
            <label class="form-check-label" for="is_can_access_customer_2">No</label>
        </div>
    </div>
    {!! $errors->first('is_can_access_customer', '<div class="invalid-feedback">:message</div>') !!}
</div>

<div class="col-12">
    {{ Form::label('is_can_access_worker', 'Access Worker', ['class' => 'form-label']) }}
    <div class="form-group">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="is_can_access_worker_1" name="is_can_access_worker" value="1" {{ $user->is_can_access_worker == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="is_can_access_worker_1">Yes</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="is_can_access_worker_2" name="is_can_access_worker" value="0" {{ $user->is_can_access_worker == '0' ? 'checked' : '' }}>
            <label class="form-check-label" for="is_can_access_worker_2">No</label>
        </div>
    </div>
    {!! $errors->first('is_can_access_worker', '<div class="invalid-feedback">:message</div>') !!}
</div>

<div class="col-12">
    <button type="submit" class="btn btn-primary">Submit</button>
    <button type="reset" class="btn btn-outline-secondary">Reset</button>
</div>
@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('supervisor') }}">Supervisor</a></li>
            <li class="breadcrumb-item"><a href="{{ route('supervisor.department') }}">{{ __('Department') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">บันทึกค่าใช้จ่ายรายวัน</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        @if ($message = Session::get('success'))
        <div class="alert alert-success mb-2">
            {{ $message }}
        </div>
    @endif
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">เพิ่มบันทึกค่าใช้จ่ายรายวัน</div>
                <div class="card-body">
                    <form class="row g-3 mb-3" action="{{ request()->url() }}" method="POST" role="form" enctype="multipart/form-data">
                        
                        @csrf
                        <div class="col-12">
                            <label class="form-label" for="department_id">แผนก</label>
                            <select class="form-select" id="department_id" name="department_id">
                                <option value="">ไม่เลือก</option>
                                @foreach ( $departments as $department )
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="daily_date">วันที่</label>
                            <input type="date" class="form-control" id="daily_date" name="daily_date" />
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="cost">จำนวนเงิน</label>
                            <input type="number" class="form-control" id="cost" name="cost" />
                        </div>

                        <div class="col-12">
                            {{ Form::label('message', 'บันทึกข้อความ - Note', ['class' => "form-label"]) }}
                            {{ Form::textarea('message', '', ['class' => 'form-control' . ($errors->has('message') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                            {!! $errors->first('message', '<div class="invalid-feedback">:message</div>') !!}
                        </div>

                        <div class="col-12">
                            {{ Form::label('image_upload', 'อัพโหลดรูป - Attach Photo', ['class' => "form-label"]) }}
                            {{ Form::file('image_upload', ['class' => 'form-control' . ($errors->has('image_upload') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                            {!! $errors->first('image_upload', '<div class="invalid-feedback">:message</div>') !!}
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('template_title')
    Create Note
@endsection

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Admin') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ __('Washing Machine') }}</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">เพิ่มบันทึก - Create Note</div>
                <div class="card-body">
                    <form class="row g-3 mb-3" action="{{ request()->url() }}" method="POST" role="form" enctype="multipart/form-data">
                        {{ Form::hidden('washing_machine_id', $washingMachine->id) }}

                        @csrf

                        <div class="col-12">
                            {{ Form::label('tag', 'ประเภทค่าใช้จ่าย - Category', ['class' => "form-label"]) }}
                            <select name="tag" id="tag" class="form-select {{ $errors->has('tag') ? 'is-invalid' : '' }}">
                                <option value="">-- เลือกประเภท --</option>
                                @foreach(\App\Models\Note::getAvailableTags() as $value => $label)
                                    <option value="{{ $value }}" {{ old('tag') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('tag', '<div class="invalid-feedback">:message</div>') !!}
                        </div>

                        <div class="col-12">
                            {{ Form::label('message', 'บันทึกข้อความ - Note', ['class' => "form-label"]) }}
                            {{ Form::textarea('message', $note->message, ['class' => 'form-control' . ($errors->has('message') ? ' is-invalid' : ''), 'placeholder' => '', 'rows' => 3]) }}
                            {!! $errors->first('message', '<div class="invalid-feedback">:message</div>') !!}
                        </div>

                        <div class="col-12">
                            {{ Form::label('cost', 'ค่าใช้จ่าย - Cost (Thai Baht)', ['class' => "form-label"]) }}
                            {{ Form::text('cost', $note->cost, ['class' => 'form-control' . ($errors->has('cost') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                            {!! $errors->first('cost', '<div class="invalid-feedback">:message</div>') !!}
                        </div>

                        <div class="col-12">
                            {{ Form::label('image_upload', 'อัพโหลดรูป - Attach Photo', ['class' => "form-label"]) }}
                            {{ Form::file('image_upload', ['class' => 'form-control' . ($errors->has('image_upload') ? ' is-invalid' : ''), 'placeholder' => '']) }}
                            {!! $errors->first('image_upload', '<div class="invalid-feedback">:message</div>') !!}
                        </div>

                        <div class="col-12">
                            <label for="note_date" class="form-label">วันที่บันทึก</label>
                            <input type="date" name="note_date" class="form-control" id="note_date">
                            @error('note_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
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


@extends('layouts.app')

@section('template_title')
    Create Note
@endsection

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Admin') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ __('Dryer Machine') }}</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">เพิ่มบันทึก - Create Note</div>
                <div class="card-body">
                    <form class="row g-3 mb-3" action="{{ request()->url() }}" method="POST" role="form" enctype="multipart/form-data">
                        {{ Form::hidden('dryer_machine_id', $dryerMachine->id) }}

                        @csrf

                        <div class="col-12">
                            {{ Form::label('tags', 'Tags (คั่นด้วย comma เช่น ค่าซ่อม, ค่าอะไหล่)', ['class' => "form-label"]) }}
                            <input type="text" 
                                   name="tags" 
                                   id="tags" 
                                   class="form-control {{ $errors->has('tags') ? 'is-invalid' : '' }}" 
                                   placeholder="พิมพ์ tags คั่นด้วย comma เช่น ค่าซ่อม, ค่าอะไหล่, ค่าแรง"
                                   value="{{ old('tags') }}"
                                   autocomplete="off">
                            @if(count($existingTags) > 0)
                            <div class="mt-2">
                                <small class="text-muted">Tags ที่เคยใช้ (คลิกเพื่อเพิ่ม):</small>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    @foreach($existingTags as $tag)
                                        <span class="badge bg-secondary tag-suggestion" 
                                              style="cursor: pointer;"
                                              onclick="addTag('{{ $tag }}')">
                                            #{{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            {!! $errors->first('tags', '<div class="invalid-feedback">:message</div>') !!}
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

<script>
function addTag(tag) {
    const input = document.getElementById('tags');
    const currentValue = input.value.trim();
    
    // Check if tag already exists
    const existingTags = currentValue.split(',').map(t => t.trim()).filter(t => t);
    if (existingTags.includes(tag)) {
        return;
    }
    
    if (currentValue) {
        input.value = currentValue + ', ' + tag;
    } else {
        input.value = tag;
    }
}
</script>
@endsection

@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.energy-resource') }}">{{ __('Energy Resource') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.energy-resource.select-employee', ['energyResourceLogId' => $energyResourceLog->id]) }}">{{ __('Energy Resource') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">บันทึกการใช้น้ำ</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">ปริมาณการใช้น้ำล่าสุด -</div>
                <div class="card-body">
 
                    <form class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ request()->url() }}" method="GET">
                        
                        <div class="col-12">
                            <div class="input-group">
                                <input type="date" name="date_start_at" value="{{ $dateStartAt }}" class="form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="date" name="date_end_at" value="{{ $dateEndAt }}" class="form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit Filter By Date</button>
                            <a href="{{ route('worker.energy-resource.log.submit-water', ['energyResourceLogId' => $energyResourceLog->id]) }}" role="button" class="btn btn-outline-secondary">Reset Filter</a>
                        </div>
                    </form>

                    <form class="row g-3 mb-3" action="{{ request()->url() }}" method="POST" role="form" enctype="multipart/form-data">
                        
                        @csrf
                        <div class="col-12">
                            <label for="log_date" class="form-label">วันที่ - Date</label>
                            <input type="date" name="log_date" class="form-control" id="log_date">
                            {!! $errors->first('log_date', '<div class="text-danger">:message</div>') !!}
                        </div>

                        <div class="col-12">
                            <label for="value" class="form-label">ปริมาณน้ำที่ใช้ - Value</label>
                            <input type="text" name="value" class="form-control" id="value">
                            @error('value')
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
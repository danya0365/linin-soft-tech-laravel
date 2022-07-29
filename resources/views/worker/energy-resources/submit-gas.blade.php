@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.energy-resource') }}">{{ __('Energy Resource') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.energy-resource.select-employee', ['energyResourceLogId' => $energyResourceLog->id]) }}">พนักงาน: {{$energyResourceLog->employee->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">บันทึกการใช้แก็ส</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">เพิ่มบันทึกการใช้แก็ส</div>
                <div class="card-body">
                    <form class="row g-3 mb-3" action="{{ request()->url() }}" method="POST" role="form" enctype="multipart/form-data">
                        
                        @csrf

                        <div class="col-12">
                            <label for="value" class="form-label">ปริมาณกิโลกรัมแก๊สที่ใช้ (kg/gas)</label>
                            <input type="text" name="value" class="form-control" id="value">
                            @error('value')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="cost" class="form-label">จำนวนเงิน - Cost (Thai Baht)</label>
                            <input type="text" name="cost" class="form-control" id="cost">
                            @error('cost')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="created_at" class="form-label">วันที่</label>
                            <input type="date" name="created_at" class="form-control" id="created_at">
                            @error('created_at')
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
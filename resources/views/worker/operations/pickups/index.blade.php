@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
          <li class="breadcrumb-item"><a href="{{ route('worker.operation') }}">ปฏิบัติการ</a></li>
          <li class="breadcrumb-item active" aria-current="page">Pick Up</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('โรงพยาบาล') }}</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-4">
                            <div class="p-3 border bg-light">โรงพยาบาล A</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 border bg-light">โรงพยาบาล B</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 border bg-light">โรงพยาบาล C</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 border bg-light">โรงพยาบาล D</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 border bg-light">โรงพยาบาล E</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 border bg-light">โรงพยาบาล F</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
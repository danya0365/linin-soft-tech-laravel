@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
          <li class="breadcrumb-item active" aria-current="page">ปฏิบัติการ</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('ปฏิบัติการ') }}</div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('worker.operation-v1.pick-up') }}" role="button">{{ __('รับสินค้า') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('worker.operation-v1.wash') }}" role="button">{{ __('ซัก') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('worker.operation-v1.dry') }}" role="button">{{ __('อบ') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('worker.operation-v1.iron') }}" role="button">{{ __('รีด') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('worker.operation-v1.packing') }}" role="button">{{ __('พับแพ็ค') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('worker.operation-v1.collect') }}" role="button">{{ __('จัดเก็บ') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
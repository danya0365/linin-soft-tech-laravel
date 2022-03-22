@extends('layouts.worker')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Worker Menu') }}</div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('worker.product') }}" role="button">{{ __('สินค้า') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('worker.customer') }}" role="button">{{ __('ลูกค้า') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('worker.operation') }}" role="button">{{ __('ปฏิบัติการ') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('worker.energy') }}" role="button">{{ __('พลังงาน') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('worker.employee') }}" role="button">{{ __('พนักงาน') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('worker.stock') }}" role="button">{{ __('สต๊อก') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
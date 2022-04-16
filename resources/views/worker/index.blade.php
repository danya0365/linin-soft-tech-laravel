@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Worker</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">{{ __('Worker Menu') }}</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-4">
                            <a href="{{ route('worker.product') }}">
                                <div class="p-3 border bg-light" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center">
                                        <div class="bi bi-stack" style="font-size: 3em"></div>
                                    </div>
                                    <div class="text-center">{{ __('สินค้า') }}</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('worker.customer') }}">
                                <div class="p-3 border bg-light" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center">
                                        <div class="bi bi-building" style="font-size: 3em"></div>
                                    </div>
                                    <div class="text-center">{{ __('ลูกค้า') }}</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('worker.operation') }}">
                                <div class="p-3 border bg-light" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center">
                                        <div class="bi bi-journals" style="font-size: 3em"></div>
                                    </div>
                                    <div class="text-center">{{ __('ปฏิบัติการ') }}</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('worker.energy-resource') }}">
                                <div class="p-3 border bg-light" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center">
                                        <div class="bi bi-battery-charging" style="font-size: 3em"></div>
                                    </div>
                                    <div class="text-center">{{ __('พลังงาน') }}</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('worker.employee') }}">
                                <div class="p-3 border bg-light" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center">
                                        <div class="bi bi-people-fill" style="font-size: 3em"></div>
                                    </div>
                                    <div class="text-center">{{ __('พนักงาน') }}</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('worker.stock') }}">
                                <div class="p-3 border bg-light" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-warehouse" style="font-size: 3em"></div>
                                            <div class="text-center mt-3">{{ __('สต๊อก - Stock') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
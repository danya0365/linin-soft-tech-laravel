@extends('layouts.supervisor')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Manager') }}</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">{{ __('Manager Menu') }}</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-12">
                            <a href="{{ route('manager.report') }}">
                                <div class="p-3 border bg-navy" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-chart-pie text-light" style="font-size: 3em"></div>
                                            <div class="text-center mt-3 text-light">{{ __('รายงานสถิติ - Report') }}</div>
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
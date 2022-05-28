@extends('layouts.app')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/">Home</a></li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header">{{ __('Home Menu') }}</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-4">
                            <a href="{{ route('worker') }}">
                                <div class="p-3 border bg-light" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-person-digging" style="font-size: 3em"></div>
                                            <div class="text-center mt-3">{{ __('คนงาน - Worker') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('supervisor') }}">
                                <div class="p-3 border bg-light" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-people-roof" style="font-size: 3em"></div>
                                            <div class="text-center mt-3">{{ __('ผู้คุม - Supervisor') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('setting') }}">
                                <div class="p-3 border bg-light" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-gears" style="font-size: 3em"></div>
                                            <div class="text-center mt-3">{{ __('ตั้งค่า - Setting') }}</div>
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

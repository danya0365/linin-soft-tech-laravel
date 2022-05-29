@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('supervisor') }}">Supervisor</a></li>
          <li class="breadcrumb-item active" aria-current="page">Department</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">Department</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-12">
                            <a href="{{ route('supervisor.department.submit-daily-expense') }}">
                                <div class="p-3 border bg-light" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 150px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-money-bill" style="font-size: 3em"></div>
                                            <div class="text-center mt-3">{{ __('เพิ่มค่าใช้จ่ายรายวัน - Daily Expense') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-12">
                            <a href="{{ route('supervisor.department.daily-expense-log') }}">
                                <div class="p-3 border bg-light" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 150px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-history" style="font-size: 3em"></div>
                                            <div class="text-center mt-3">{{ __('ประวัติค่าใช้จ่ายรายวัน - Daily Expense Logs') }}</div>
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
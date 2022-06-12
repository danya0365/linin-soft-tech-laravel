@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
          <li class="breadcrumb-item active" aria-current="page">เลือกพนักงาน</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        @foreach ($departments as $department)
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">{{ $department['name'] }}</div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach ($department['employees'] as $employee)
                        <div class="col-sm-4">
                            <a href="{{ route('worker.employee.employee-summary', ['employeeId' => $employee['id']]) }}">
                                <div class="p-3 border bg-light" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center">
                                        <div style="max-width: 150px">
                                            <x-employee-avatar :photo="$employee['photo']" />
                                        </div>
                                    </div>
                                    <div class="text-center">{{ $employee['name'] }}</div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
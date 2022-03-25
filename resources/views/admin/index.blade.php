@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
          <li class="breadcrumb-item active" aria-current="page">Index</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Admin Menu') }}</div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('users.index') }}" role="button">{{ __('ไอดีล็อกอิน') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('customer-groups.index') }}" role="button">{{ __('กลุ่มลูกค้า') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('customers.index') }}" role="button">{{ __('ลูกค้า') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('departments.index') }}" role="button">{{ __('แผนกพนักงาน') }}</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('employees.index') }}" role="button">{{ __('พนักงาน') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
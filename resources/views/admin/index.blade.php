@extends('layouts.admin')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Admin</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">{{ __('Admin Menu') }}</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-4">
                            <a href="{{ route('users.index') }}">
                                <div class="p-3 border bg-navy" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-people-group text-light" style="font-size: 3em"></div>
                                            <div class="text-center mt-3 text-light">{{ __('ไอดีล็อกอิน') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('customer-groups.index') }}">
                                <div class="p-3 border bg-navy" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-building-user text-light" style="font-size: 3em"></div>
                                            <div class="text-center mt-3 text-light">{{ __('กลุ่มลูกค้า') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('customers.index') }}">
                                <div class="p-3 border bg-navy" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-building-user text-light" style="font-size: 3em"></div>
                                            <div class="text-center mt-3 text-light">{{ __('ลูกค้า') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('departments.index') }}">
                                <div class="p-3 border bg-navy" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-people-roof text-light" style="font-size: 3em"></div>
                                            <div class="text-center mt-3 text-light">{{ __('แผนกพนักงาน') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('employees.index') }}">
                                <div class="p-3 border bg-navy" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-person-digging text-light" style="font-size: 3em"></div>
                                            <div class="text-center mt-3 text-light">{{ __('พนักงาน') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('linen-types.index') }}">
                                <div class="p-3 border bg-navy" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-shirt text-light" style="font-size: 3em"></div>
                                            <div class="text-center mt-3 text-light">{{ __('ชนิดผ้า') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('linen-products.index') }}">
                                <div class="p-3 border bg-navy" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-shirt text-light" style="font-size: 3em"></div>
                                            <div class="text-center mt-3 text-light">{{ __('ผ้า') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('washing-machines.index') }}">
                                <div class="p-3 border bg-navy" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-shirt text-light" style="font-size: 3em"></div>
                                            <div class="text-center mt-3 text-light">{{ __('เครื่องซักผ้า') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('dryer-machines.index') }}">
                                <div class="p-3 border bg-navy" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-fire text-light" style="font-size: 3em"></div>
                                            <div class="text-center mt-3 text-light">{{ __('เครื่องอบผ้า') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('trucks.index') }}">
                                <div class="p-3 border bg-navy" style="min-height: 150px">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="min-height: 110px">
                                        <div class="text-center">
                                            <div class="fa-solid fa-truck text-light" style="font-size: 3em"></div>
                                            <div class="text-center mt-3 text-light">{{ __('รถบรรทุก') }}</div>
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
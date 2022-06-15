@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('supervisor') }}">Supervisor</a></li>
            <li class="breadcrumb-item"><a href="{{ route('supervisor.customer') }}">Customer</a></li>
            <li class="breadcrumb-item active" aria-current="page">New Billing</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">น้ำหนักลูกค้าและยอดเก็บเงิน</div>
                <div class="card-body">
                    <form method="POST" class="row g-3 mb-3" action="{{ route('supervisor.customer.new-billing') }}"  role="form" enctype="multipart/form-data">
                        
                        @csrf
                        <div class="col-12">
                            <label for="customer_id" class="form-label">ลูกค้า</label>
                            <select class="form-select" id="customer_id" name="customer_id">
                                <option value="">เลือกลูกค้า</option>
                                @foreach ( $customerGroups as $customerGroup )
                                <optgroup label="{{ $customerGroup['name'] }}">
                                    @foreach ( $customerGroup['customers'] as $customer )
                                    <option value="{{ $customer['id'] }}">{{ $customer['name'] }}</option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-12">
                            <label for="total_billing_weight" class="form-label">น้ำหนักลูกค้า (kg.)</label>
                            <input type="number" step=".01" name="total_billing_weight" class="form-control" id="value">
                            @error('total_billing_weight')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="total_billing_payment" class="form-label">ยอดเก็บเงิน (Thai Baht)</label>
                            <input type="number" step=".01" name="total_billing_payment" class="form-control" id="cost">
                            @error('total_billing_payment')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="billing_payment_date" class="form-label">วันที่เก็บเงิน</label>
                            <input type="date" name="billing_payment_date" class="form-control" id="billing_payment_date">
                            @error('billing_payment_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('supervisor') }}">Supervisor</a></li>
            <li class="breadcrumb-item"><a href="{{ route('supervisor.customer') }}">{{ __('Customer') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('ประวัติบิลรายรับ - Billing Logs') }}</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">

            @if ($message = Session::get('success'))
            <div class="alert alert-success mb-2">
                {{ $message }}
            </div>
            @endif
            
            <div class="card">
                <div class="card-header">{{ __('ประวัติบิลรายรับ - Billing Logs') }}</div>
                <div class="card-body">
 
                    <form class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ request()->url() }}" method="GET">

                        <div class="col-12">
                            <div class="input-group">
                                <label class="input-group-text" for="customer_id">ลูกค้า</label>
                                <select class="form-select" id="customer_id" name="customer_id" onchange="this.form.submit()">
                                    <option value="">แสดงทั้งหมด - Show All</option>
                                    @foreach ( $customerGroups as $customerGroup )
                                    <optgroup label="{{ $customerGroup['name'] }}">
                                        @foreach ( $customerGroup['customers'] as $customer )
                                        <option value="{{ $customer['id'] }}" {{ $customerIdSelected == $customer['id'] ? 'selected' : '' }}>{{ $customer['name'] }}</option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="input-group">
                                <input type="date" name="date_start_at" value="{{ $dateStartAt }}" class="form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="date" name="date_end_at" value="{{ $dateEndAt }}" class="form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group">
                                <label class="input-group-text" for="sort_order">เรียงโดย</label>
                                <select class="form-select" id="sort_order" name="sort_order" onchange="this.form.submit()">
                                    @foreach ( $sortOrders as $sortOrder )
                                    <option value="{{ $sortOrder['var'] }}" {{ $sortOrderSelected == $sortOrder['var'] ? 'selected' : '' }}>{{ $sortOrder['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ request()->url() }}" role="button" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead">
                                <tr>
                                    <th>วันที่บันทึก</th>
                                    <th>ลูกค้า</th>
                                    <th>น้ำหนักที่ลูกค้า (kg.)</th>
                                    <th>จำนวนเงิน (Thai Baht)</th>
                                    <th>วันที่เก็บเงิน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($billingLogs as $billingLog)
                                    <tr>
                                        <td>{{ $billingLog->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $billingLog->customer->name ?? '-' }}</td>
                                        <td class="text-center">
                                            {{ number_format($billingLog->total_billing_weight) }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($billingLog->total_billing_payment) }}
                                        </td>
                                        <td class="text-center">{{ $billingLog->billing_payment_date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer">
                    {!! $billingLogs->withQueryString()->links() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    $('.delete-form').on('submit', function(e){
        if (!confirm("Are you sure?")) {
            return false;
        }
        return true
    })
});
 </script>
@endsection
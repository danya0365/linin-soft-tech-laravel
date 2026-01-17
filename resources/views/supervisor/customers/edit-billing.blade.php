@extends('layouts.supervisor')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('supervisor') }}">Supervisor</a></li>
            <li class="breadcrumb-item"><a href="{{ route('supervisor.customer') }}">{{ __('Customer') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('supervisor.customer.billing-logs') }}">{{ __('Billing Logs') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('แก้ไขบิลรายรับ - Edit Billing') }}</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">แก้ไขบิลรายรับ - ลูกค้า: {{ $operation->customer->name ?? '-' }}</div>
                <div class="card-body">
                    <form method="POST" class="row g-3 mb-3" action="{{ route('supervisor.customer.billing-logs.edit', $operation->id) }}" role="form" enctype="multipart/form-data">
                        
                        @csrf
                        
                        <div class="col-12">
                            <label class="form-label">ลูกค้า</label>
                            <input type="text" class="form-control" value="{{ $operation->customer->name ?? '-' }}" disabled>
                            <small class="text-muted">ไม่สามารถเปลี่ยนลูกค้าได้</small>
                        </div>

                        <div class="col-12">
                            <label for="total_billing_weight" class="form-label">น้ำหนักลูกค้า (kg.)</label>
                            <input type="number" step=".01" name="total_billing_weight" class="form-control" id="total_billing_weight" value="{{ $operation->total_billing_weight }}">
                            @error('total_billing_weight')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="total_wet_weight" class="form-label">น้ำหนักผ้าเปียก (kg.)</label>
                            <input type="number" step=".01" name="total_wet_weight" class="form-control" id="total_wet_weight" value="{{ $operation->total_wet_weight }}" oninput="calculateWeightDiff()">
                            @error('total_wet_weight')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="total_dry_weight" class="form-label">น้ำหนักผ้าแห้ง (kg.)</label>
                            <input type="number" step=".01" name="total_dry_weight" class="form-control" id="total_dry_weight" value="{{ $operation->total_dry_weight }}" oninput="calculateWeightDiff()">
                            @error('total_dry_weight')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">% หักลบ (เปียก-แห้ง)</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="weight_diff_percent" readonly>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">คำนวณอัตโนมัติ</small>
                        </div>

                        <div class="col-12">
                            <label for="total_billing_payment" class="form-label">ยอดเก็บเงิน (Thai Baht)</label>
                            <input type="number" step=".01" class="form-control bg-light" id="total_billing_payment" value="{{ $operation->total_billing_payment }}" disabled>
                            <small class="text-warning"><i class="fa fa-lock"></i> ไม่สามารถแก้ไขได้ เพราะผูกกับรายได้ในระบบ</small>
                        </div>

                        <div class="col-12">
                            <label for="billing_payment_date" class="form-label">วันที่เก็บเงิน</label>
                            <input type="date" class="form-control bg-light" id="billing_payment_date" value="{{ $operation->billing_payment_date }}" disabled>
                            <small class="text-warning"><i class="fa fa-lock"></i> ไม่สามารถแก้ไขได้ เพราะผูกกับรายงานสรุปรายวัน</small>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                            <a href="{{ route('supervisor.customer.billing-logs') }}" class="btn btn-outline-secondary">ยกเลิก</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateWeightDiff() {
    var wetWeight = parseFloat(document.getElementById('total_wet_weight').value) || 0;
    var dryWeight = parseFloat(document.getElementById('total_dry_weight').value) || 0;
    var diffPercent = 0;
    
    if (wetWeight > 0 && dryWeight > 0) {
        diffPercent = ((wetWeight - dryWeight) / wetWeight * 100).toFixed(2);
    }
    
    document.getElementById('weight_diff_percent').value = diffPercent;
}

// คำนวณ % เมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', function() {
    calculateWeightDiff();
});
</script>
@endsection

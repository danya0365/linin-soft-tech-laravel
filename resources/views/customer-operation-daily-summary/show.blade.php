@extends('layouts.app')

@section('template_title')
    {{ $customerOperationDailySummary->name ?? 'Show Customer Operation Daily Summary' }}
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Customer Operation Daily Summary</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('customer-operation-daily-summaries.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Customer Id:</strong>
                            {{ $customerOperationDailySummary->customer_id }}
                        </div>
                        <div class="form-group">
                            <strong>Operation Date:</strong>
                            {{ $customerOperationDailySummary->operation_date }}
                        </div>
                        <div class="form-group">
                            <strong>Total Wet Weight:</strong>
                            {{ $customerOperationDailySummary->total_wet_weight }}
                        </div>
                        <div class="form-group">
                            <strong>Total Dry Weight:</strong>
                            {{ $customerOperationDailySummary->total_dry_weight }}
                        </div>
                        <div class="form-group">
                            <strong>Total Iron Piece:</strong>
                            {{ $customerOperationDailySummary->total_iron_piece }}
                        </div>
                        <div class="form-group">
                            <strong>Total Packing Piece:</strong>
                            {{ $customerOperationDailySummary->total_packing_piece }}
                        </div>
                        <div class="form-group">
                            <strong>Edit Weight (ระบบ):</strong>
                            <span class="text-success" title="คำนวณอัตโนมัติจาก linen_case='edit'">{{ $customerOperationDailySummary->total_edit_collect_weight }} <i class="fa fa-info-circle"></i></span>
                        </div>
                        <div class="form-group">
                            <strong>Edit Weight (บันทึกมือ):</strong>
                            <span class="text-warning" title="กรอกโดย Supervisor ตอนออกบิล">{{ $customerOperationDailySummary->total_edit_weight ?? 0 }} <i class="fa fa-info-circle"></i></span>
                        </div>
                        <div class="form-group">
                            <strong>Total Collect Weight:</strong>
                            {{ $customerOperationDailySummary->total_collect_weight }}
                        </div>
                        <div class="form-group">
                            <strong>Total Billing Weight:</strong>
                            {{ $customerOperationDailySummary->total_billing_weight }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

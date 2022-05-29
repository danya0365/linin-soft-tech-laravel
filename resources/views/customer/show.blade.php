@extends('layouts.app')

@section('template_title')
    {{ $customer->name ?? 'Show Customer' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Customer</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('customers.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $customer->name }}
                        </div>
                        <div class="form-group">
                            <strong>Customer Group Id:</strong>
                            {{ $customer->customer_group_id }}
                        </div>
                        <div class="form-group">
                            <strong>Total Wet Weight:</strong>
                            {{ $customer->total_wet_weight }}
                        </div>
                        <div class="form-group">
                            <strong>Total Dry Weight:</strong>
                            {{ $customer->total_dry_weight }}
                        </div>
                        <div class="form-group">
                            <strong>Total Billing Weight:</strong>
                            {{ $customer->total_billing_weight }}
                        </div>
                        <div class="form-group">
                            <strong>Total Edit Weight:</strong>
                            {{ $customer->total_edit_weight }}
                        </div>
                        <div class="form-group">
                            <strong>Total Billing Payment:</strong>
                            {{ $customer->total_billing_payment }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

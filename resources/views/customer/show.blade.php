@extends('layouts.app')

@section('template_title')
    {{ $customer->name ?? 'Show Customer' }}
@endsection

@section('content')
    <section class="content container">
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
                            <strong>Customer Group:</strong>
                            {{ $customer->customerGroup ? $customer->customerGroup->name : '-' }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

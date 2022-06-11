@extends('layouts.app')

@section('template_title')
    {{ $operation->name ?? 'Show Operation' }}
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Operation</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('operations.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Operation Type:</strong>
                            {{ $operation->operation_type }}
                        </div>
                        <div class="form-group">
                            <strong>Employee Id:</strong>
                            {{ $operation->employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Customer Id:</strong>
                            {{ $operation->customer_id }}
                        </div>
                        <div class="form-group">
                            <strong>Wash Employee Id:</strong>
                            {{ $operation->wash_employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Dry Employee Id:</strong>
                            {{ $operation->dry_employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Iron Employee Id:</strong>
                            {{ $operation->iron_employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Packing Employee Id:</strong>
                            {{ $operation->packing_employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Collect Employee Id:</strong>
                            {{ $operation->collect_employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Job Case:</strong>
                            {{ $operation->job_case }}
                        </div>
                        <div class="form-group">
                            <strong>Washing Machine Id:</strong>
                            {{ $operation->washing_machine_id }}
                        </div>
                        <div class="form-group">
                            <strong>Dryer Machine Id:</strong>
                            {{ $operation->dryer_machine_id }}
                        </div>
                        <div class="form-group">
                            <strong>Total Wet Weight:</strong>
                            {{ $operation->total_wet_weight }}
                        </div>
                        <div class="form-group">
                            <strong>Total Dry Weight:</strong>
                            {{ $operation->total_dry_weight }}
                        </div>
                        <div class="form-group">
                            <strong>Total Iron Piece:</strong>
                            {{ $operation->total_iron_piece }}
                        </div>
                        <div class="form-group">
                            <strong>Total Packing Piece:</strong>
                            {{ $operation->total_packing_piece }}
                        </div>
                        <div class="form-group">
                            <strong>Colors:</strong>
                            {{ $operation->colors }}
                        </div>
                        <div class="form-group">
                            <strong>Search Tags:</strong>
                            {{ $operation->search_tags }}
                        </div>
                        <div class="form-group">
                            <strong>Status:</strong>
                            {{ $operation->status }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

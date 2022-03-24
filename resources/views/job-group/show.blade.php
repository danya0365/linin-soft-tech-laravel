@extends('layouts.app')

@section('template_title')
    {{ $jobGroup->name ?? 'Show Job Group' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Job Group</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('job-groups.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Customer Id:</strong>
                            {{ $jobGroup->customer_id }}
                        </div>
                        <div class="form-group">
                            <strong>Employee Id:</strong>
                            {{ $jobGroup->employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Wet Weight:</strong>
                            {{ $jobGroup->wet_weight }}
                        </div>
                        <div class="form-group">
                            <strong>Dry Weight:</strong>
                            {{ $jobGroup->dry_weight }}
                        </div>
                        <div class="form-group">
                            <strong>Total Pieces:</strong>
                            {{ $jobGroup->total_pieces }}
                        </div>
                        <div class="form-group">
                            <strong>Operation Status:</strong>
                            {{ $jobGroup->operation_status }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

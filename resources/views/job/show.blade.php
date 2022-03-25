@extends('layouts.app')

@section('template_title')
    {{ $job->name ?? 'Show Job' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Job</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('jobs.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Job Group Id:</strong>
                            {{ $job->job_group_id }}
                        </div>
                        <div class="form-group">
                            <strong>Customer Id:</strong>
                            {{ $job->customer_id }}
                        </div>
                        <div class="form-group">
                            <strong>Employee Id:</strong>
                            {{ $job->employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Job Type:</strong>
                            {{ $job->job_type }}
                        </div>
                        <div class="form-group">
                            <strong>Washing Machine Id:</strong>
                            {{ $job->washing_machine_id }}
                        </div>
                        <div class="form-group">
                            <strong>Dryer Machine Id:</strong>
                            {{ $job->dryer_machine_id }}
                        </div>
                        <div class="form-group">
                            <strong>Linen Type Id:</strong>
                            {{ $job->linen_type_id }}
                        </div>
                        <div class="form-group">
                            <strong>Wet Weight:</strong>
                            {{ $job->wet_weight }}
                        </div>
                        <div class="form-group">
                            <strong>Color:</strong>
                            {{ $job->color }}
                        </div>
                        <div class="form-group">
                            <strong>Status:</strong>
                            {{ $job->status }}
                        </div>
                        <div class="form-group">
                            <strong>Tags:</strong>
                            {{ $job->tags }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

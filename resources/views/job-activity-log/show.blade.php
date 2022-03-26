@extends('layouts.app')

@section('template_title')
    {{ $jobActivityLog->name ?? 'Show Job Activity Log' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Job Activity Log</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('job-activity-logs.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Job Id:</strong>
                            {{ $jobActivityLog->job_id }}
                        </div>
                        <div class="form-group">
                            <strong>Employee Id:</strong>
                            {{ $jobActivityLog->employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Log Type:</strong>
                            {{ $jobActivityLog->log_type }}
                        </div>
                        <div class="form-group">
                            <strong>Old Value:</strong>
                            {{ $jobActivityLog->old_value }}
                        </div>
                        <div class="form-group">
                            <strong>New Value:</strong>
                            {{ $jobActivityLog->new_value }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

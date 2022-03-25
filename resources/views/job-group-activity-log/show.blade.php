@extends('layouts.app')

@section('template_title')
    {{ $jobGroupActivityLog->name ?? 'Show Job Group Activity Log' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Job Group Activity Log</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('job-group-activity-logs.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Job Group Id:</strong>
                            {{ $jobGroupActivityLog->job_group_id }}
                        </div>
                        <div class="form-group">
                            <strong>Employee Id:</strong>
                            {{ $jobGroupActivityLog->employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Log Type:</strong>
                            {{ $jobGroupActivityLog->log_type }}
                        </div>
                        <div class="form-group">
                            <strong>Old Value:</strong>
                            {{ $jobGroupActivityLog->old_value }}
                        </div>
                        <div class="form-group">
                            <strong>New Value:</strong>
                            {{ $jobGroupActivityLog->new_value }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

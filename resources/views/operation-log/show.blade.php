@extends('layouts.app')

@section('template_title')
    {{ $operationLog->name ?? 'Show Operation Log' }}
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Operation Log</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('operation-logs.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Operation Id:</strong>
                            {{ $operationLog->operation_id }}
                        </div>
                        <div class="form-group">
                            <strong>Employee Id:</strong>
                            {{ $operationLog->employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Action Name:</strong>
                            {{ $operationLog->action_name }}
                        </div>
                        <div class="form-group">
                            <strong>Old Values:</strong>
                            {{ $operationLog->old_values }}
                        </div>
                        <div class="form-group">
                            <strong>New Values:</strong>
                            {{ $operationLog->new_values }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

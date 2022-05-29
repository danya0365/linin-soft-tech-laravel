@extends('layouts.app')

@section('template_title')
    {{ $employeeOperationLog->name ?? 'Show Employee Operation Log' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Employee Operation Log</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('employee-operation-logs.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Employee Id:</strong>
                            {{ $employeeOperationLog->employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Operation Type:</strong>
                            {{ $employeeOperationLog->operation_type }}
                        </div>
                        <div class="form-group">
                            <strong>Action Type:</strong>
                            {{ $employeeOperationLog->action_type }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

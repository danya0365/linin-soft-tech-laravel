@extends('layouts.app')

@section('template_title')
    {{ $employeeWorkingTime->name ?? 'Show Employee Working Time' }}
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Employee Working Time</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('employee-working-times.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Employee Id:</strong>
                            {{ $employeeWorkingTime->employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Working Date:</strong>
                            {{ $employeeWorkingTime->working_date }}
                        </div>
                        <div class="form-group">
                            <strong>Time Duration:</strong>
                            {{ $employeeWorkingTime->time_duration }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

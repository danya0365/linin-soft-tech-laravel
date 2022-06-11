@extends('layouts.app')

@section('template_title')
    {{ $employee->name ?? 'Show Employee' }}
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Employee</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('employees.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Code:</strong>
                            {{ $employee->code }}
                        </div>
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $employee->name }}
                        </div>
                        <div class="form-group">
                            <strong>Photo:</strong>
                            {{ $employee->photo }}
                        </div>
                        <div class="form-group">
                            <strong>Department:</strong>
                            {{ $employee->department ? $employee->department->name : '-' }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

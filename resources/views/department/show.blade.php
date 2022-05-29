@extends('layouts.app')

@section('template_title')
    {{ $department->name ?? 'Show Department' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Department</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('departments.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Var Name:</strong>
                            {{ $department->var_name }}
                        </div>
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $department->name }}
                        </div>
                        <div class="form-group">
                            <strong>Input Unit:</strong>
                            {{ $department->input_unit }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

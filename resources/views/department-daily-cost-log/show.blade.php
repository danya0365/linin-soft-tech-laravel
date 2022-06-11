@extends('layouts.app')

@section('template_title')
    {{ $departmentDailyCostLog->name ?? 'Show Department Daily Cost Log' }}
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Department Daily Cost Log</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('department-daily-cost-logs.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Department Id:</strong>
                            {{ $departmentDailyCostLog->department_id }}
                        </div>
                        <div class="form-group">
                            <strong>Daily Date:</strong>
                            {{ $departmentDailyCostLog->daily_date }}
                        </div>
                        <div class="form-group">
                            <strong>Cost:</strong>
                            {{ $departmentDailyCostLog->cost }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

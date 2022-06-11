@extends('layouts.app')

@section('template_title')
    {{ $energyResourceLog->name ?? 'Show Energy Resource Log' }}
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Energy Resource Log</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('energy-resource-logs.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Energy Resource Id:</strong>
                            {{ $energyResourceLog->energy_resource_id }}
                        </div>
                        <div class="form-group">
                            <strong>Employee Id:</strong>
                            {{ $energyResourceLog->employee_id }}
                        </div>
                        <div class="form-group">
                            <strong>Value:</strong>
                            {{ $energyResourceLog->value }}
                        </div>
                        <div class="form-group">
                            <strong>Unit:</strong>
                            {{ $energyResourceLog->unit }}
                        </div>
                        <div class="form-group">
                            <strong>Lot Number:</strong>
                            {{ $energyResourceLog->lot_number }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.app')

@section('template_title')
    {{ $dryerMachine->name ?? 'Show Dryer Machine' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Dryer Machine</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('dryer-machines.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $dryerMachine->name }}
                        </div>
                        <div class="form-group">
                            <strong>Photo:</strong>
                            {{ $dryerMachine->photo }}
                        </div>
                        <div class="form-group">
                            <strong>Maximum Weight:</strong>
                            {{ $dryerMachine->maximum_weight }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

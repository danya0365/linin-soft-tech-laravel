@extends('layouts.app')

@section('template_title')
    {{ $washingMachine->name ?? 'Show Washing Machine' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Washing Machine</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('washing-machines.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $washingMachine->name }}
                        </div>
                        <div class="form-group">
                            <strong>Photo:</strong>
                            {{ $washingMachine->photo }}
                        </div>
                        <div class="form-group">
                            <strong>Maximum Weight:</strong>
                            {{ $washingMachine->maximum_weight }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

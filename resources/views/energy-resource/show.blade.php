@extends('layouts.app')

@section('template_title')
    {{ $energyResource->name ?? 'Show Energy Resource' }}
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Energy Resource</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('energy-resources.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $energyResource->name }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

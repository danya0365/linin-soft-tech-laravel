@extends('layouts.app')

@section('template_title')
    {{ $truck->name ?? 'Show Truck' }}
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Truck</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('trucks.create-note') }}"> Add Note</a>
                            <a class="btn btn-primary" href="{{ route('trucks.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $truck->name }}
                        </div>
                        <div class="form-group">
                            <strong>Photo:</strong>
                            {{ $truck->photo }}
                        </div>
                        <div class="form-group">
                            <strong>Plate Number:</strong>
                            {{ $truck->plate_number }}
                        </div>
                        <div class="form-group">
                            <strong>Operation Id:</strong>
                            {{ $truck->operation_id }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.app')

@section('template_title')
    {{ $note->name ?? 'Show Note' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Note</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('notes.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Message:</strong>
                            {{ $note->message }}
                        </div>
                        <div class="form-group">
                            <strong>Image Url:</strong>
                            {{ $note->image_url }}
                        </div>
                        <div class="form-group">
                            <strong>Cost:</strong>
                            {{ $note->cost }}
                        </div>
                        <div class="form-group">
                            <strong>Washing Machine Id:</strong>
                            {{ $note->washing_machine_id }}
                        </div>
                        <div class="form-group">
                            <strong>Dryer Machine Id:</strong>
                            {{ $note->dryer_machine_id }}
                        </div>
                        <div class="form-group">
                            <strong>Truck Id:</strong>
                            {{ $note->truck_id }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.app')

@section('template_title')
    {{ $loginHistory->name ?? 'Show Login History' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Login History</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('login-histories.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>User Id:</strong>
                            {{ $loginHistory->user_id }}
                        </div>
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $loginHistory->name }}
                        </div>
                        <div class="form-group">
                            <strong>Email:</strong>
                            {{ $loginHistory->email }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

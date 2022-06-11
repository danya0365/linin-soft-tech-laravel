@extends('layouts.app')

@section('template_title')
    {{ $income->name ?? 'Show Income' }}
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Income</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('incomes.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Type Name:</strong>
                            {{ $income->type_name }}
                        </div>
                        <div class="form-group">
                            <strong>Table Name:</strong>
                            {{ $income->table_name }}
                        </div>
                        <div class="form-group">
                            <strong>Table Id:</strong>
                            {{ $income->table_id }}
                        </div>
                        <div class="form-group">
                            <strong>Amount:</strong>
                            {{ $income->amount }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

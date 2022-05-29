@extends('layouts.app')

@section('template_title')
    Create Energy Resource Log
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Create Energy Resource Log</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('energy-resource-logs.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('energy-resource-log.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

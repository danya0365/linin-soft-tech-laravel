@extends('layouts.app')

@section('template_title')
    Update Energy Resource Log
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Update Energy Resource Log</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('energy-resource-logs.update', $energyResourceLog->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('energy-resource-log.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.app')

@section('template_title')
    Create Employee Working Time
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Create Employee Working Time</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('employee-working-times.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('employee-working-time.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

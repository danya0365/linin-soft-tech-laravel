@extends('layouts.app')

@section('template_title')
    Update Employee Working Time
@endsection

@section('content')
    <section class="content container">
        <div class="">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Update Employee Working Time</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('employee-working-times.update', $employeeWorkingTime->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('employee-working-time.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

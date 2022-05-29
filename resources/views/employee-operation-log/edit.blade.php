@extends('layouts.app')

@section('template_title')
    Update Employee Operation Log
@endsection

@section('content')
    <section class="content container">
        <div class="">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Update Employee Operation Log</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('employee-operation-logs.update', $employeeOperationLog->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('employee-operation-log.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.app')

@section('template_title')
    Update Department Daily Cost Log
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Update Department Daily Cost Log</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('department-daily-cost-logs.update', $departmentDailyCostLog->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('department-daily-cost-log.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.app')

@section('template_title')
    Update Login History
@endsection

@section('content')
    <section class="content container">
        <div class="">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Update Login History</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login-histories.update', $loginHistory->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('login-history.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

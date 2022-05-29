@extends('layouts.app')

@section('template_title')
    Create Customer Operation Daily Summary
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Create Customer Operation Daily Summary</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('customer-operation-daily-summaries.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('customer-operation-daily-summary.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

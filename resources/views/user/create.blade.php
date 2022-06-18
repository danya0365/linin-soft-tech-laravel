@extends('layouts.app')

@section('template_title')
    Create User
@endsection

@section('content')
    <section class="content container">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('setting') }}">{{ __('Setting') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('User') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Create User') }}</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Create User</span>
                    </div>
                    <div class="card-body">
                        <form class="row g-3 mb-3" method="POST" action="{{ route('users.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('user.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

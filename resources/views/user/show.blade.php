@extends('layouts.app')

@section('template_title')
    {{ $user->name ?? 'Show User' }}
@endsection

@section('content')
    <section class="content container">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('setting') }}">{{ __('Setting') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('User') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Show User') }}</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show User</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('users.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $user->name }}
                        </div>
                        <div class="form-group">
                            <strong>Email:</strong>
                            {{ $user->email }}
                        </div>
                        <div class="form-group">
                            <strong>Role:</strong>
                            {{ App\Enums\UserRole::getDescription($user->role) }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

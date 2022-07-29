@extends('layouts.app')

@section('template_title')
    Update Truck
@endsection

@section('content')
    <section class="content container">
        <div class="">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Update Truck</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('trucks.update', $truck->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('truck.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

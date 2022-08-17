@extends('layouts.app')

@section('template_title')
    {{ $truck->name ?? 'Show Truck' }}
@endsection

@section('content')
    <div class="container">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Admin') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Truck') }}</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Truck</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('trucks.create-note', ['id' => $truck->id]) }}"> Add Note</a>
                            <a class="btn btn-primary" href="{{ route('trucks.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $truck->name }}
                        </div>
                        <div class="form-group">
                            <strong>Photo:</strong>
                            {{ $truck->photo }}
                        </div>
                        <div class="form-group">
                            <strong>Plate Number:</strong>
                            {{ $truck->plate_number }}
                        </div>
                        <div class="form-group">
                            <strong>Operation Id:</strong>
                            {{ $truck->operation_id }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Note') }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                    @foreach ($notes as $note)
                        <div class="card mb-2">
                            <div class="card-body">
                                <h5 class="card-title">ค่าใช้จ่าย: {{ $note->cost }}</h5>
                                <p class="card-text">{{ $note->message }}</p>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <small class="text-muted">วันที่: {{ $note->created_at->format('Y-m-d') }}</small>
                                </li>
                            </ul>
                            <div class="card-body">
                                <form action="{{ route('notes.destroy',$note->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i> Delete</button>
                                </form>
                            </div>
                            @if ( $note->image_url != '' )
                            <img src="{{ asset($note->image_url) }}" class="card-img-bottom" alt="{{ asset($note->image_url) }}">
                            @endif
                        </div>
                    @endforeach
                    </div>
                </div>
                {!! $notes->links() !!}
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('template_title')
    {{ $washingMachine->name ?? 'Show Washing Machine' }}
@endsection

@section('content')
    <section class="content container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Washing Machine</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('washing-machines.create-note', ['id' => $washingMachine->id]) }}"> Add Note</a>
                            <a class="btn btn-primary" href="{{ route('washing-machines.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $washingMachine->name }}
                        </div>
                        <div class="form-group">
                            <strong>Photo:</strong>
                            {{ $washingMachine->photo }}
                        </div>
                        <div class="form-group">
                            <strong>Maximum Weight:</strong>
                            {{ $washingMachine->maximum_weight }}
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
    </section>
@endsection

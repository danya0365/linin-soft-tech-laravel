@extends('layouts.app')

@section('template_title')
    {{ $truck->name ?? 'Show Truck' }}
@endsection

@section('content')
    <div class="container">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('setting') }}">{{ __('Setting') }}</a></li>
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
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>Date Created</th>
                                        
										<th>Message</th>
										<th>Image Url</th>
										<th>Cost</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notes as $note)
                                        <tr>
                                            <td>{{ $note->created_at->format('Y-m-d') }}</td>
                                            
											<td>{{ $note->message }}</td>
											<td>
                                                @if ( $note->image_url )
                                                <a href="{{ asset($note->image_url) }}" >
                                                    View Photo
                                                </a>
                                                @endif
                                            </td>
											<td>{{ $note->cost }}</td>

                                            <td>
                                                <form action="{{ route('notes.destroy',$note->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('notes.show',$note->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('notes.edit',$note->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i> Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $notes->links() !!}
            </div>
        </div>
    </div>
@endsection

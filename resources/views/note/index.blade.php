@extends('layouts.app')

@section('template_title')
    Note
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Note') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('notes.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
                                </a>
                              </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        
										<th>Message</th>
										<th>Image Url</th>
										<th>Cost</th>
										<th>Washing Machine Id</th>
										<th>Dryer Machine Id</th>
										<th>Truck Id</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notes as $note)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $note->message }}</td>
											<td>{{ $note->image_url }}</td>
											<td>{{ $note->cost }}</td>
											<td>{{ $note->washing_machine_id }}</td>
											<td>{{ $note->dryer_machine_id }}</td>
											<td>{{ $note->truck_id }}</td>

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

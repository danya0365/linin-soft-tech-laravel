@extends('layouts.app')

@section('template_title')
    Dryer Machine
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Dryer Machine') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('dryer-machines.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
										<th>Name</th>
										<th>Photo</th>
										<th>Maximum Weight</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dryerMachines as $dryerMachine)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $dryerMachine->name }}</td>
											<td>{{ $dryerMachine->photo }}</td>
											<td>{{ $dryerMachine->maximum_weight }}</td>

                                            <td>
                                                <form action="{{ route('dryer-machines.destroy',$dryerMachine->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('dryer-machines.show',$dryerMachine->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('dryer-machines.edit',$dryerMachine->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $dryerMachines->links() !!}
            </div>
        </div>
    </div>
@endsection

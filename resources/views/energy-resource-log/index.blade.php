@extends('layouts.app')

@section('template_title')
    Energy Resource Log
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Energy Resource Log') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('energy-resource-logs.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
                                </a>
                              </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            {{ $message }}
                        </div>
                    @endif

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        
										<th>Energy Resource Id</th>
										<th>Employee Id</th>
										<th>Value</th>
										<th>Unit</th>
										<th>Lot Number</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($energyResourceLogs as $energyResourceLog)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $energyResourceLog->energy_resource_id }}</td>
											<td>{{ $energyResourceLog->employee_id }}</td>
											<td>{{ $energyResourceLog->value }}</td>
											<td>{{ $energyResourceLog->unit }}</td>
											<td>{{ $energyResourceLog->lot_number }}</td>

                                            <td>
                                                <form action="{{ route('energy-resource-logs.destroy',$energyResourceLog->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('energy-resource-logs.show',$energyResourceLog->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('energy-resource-logs.edit',$energyResourceLog->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $energyResourceLogs->links() !!}
            </div>
        </div>
    </div>
@endsection

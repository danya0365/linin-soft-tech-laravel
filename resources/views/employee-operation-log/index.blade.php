@extends('layouts.app')

@section('template_title')
    Employee Operation Log
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Employee Operation Log') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('employee-operation-logs.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
										<th>Employee Id</th>
										<th>Operation Type</th>
										<th>Action Type</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employeeOperationLogs as $employeeOperationLog)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $employeeOperationLog->employee_id }}</td>
											<td>{{ $employeeOperationLog->operation_type }}</td>
											<td>{{ $employeeOperationLog->action_type }}</td>

                                            <td>
                                                <form action="{{ route('employee-operation-logs.destroy',$employeeOperationLog->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('employee-operation-logs.show',$employeeOperationLog->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('employee-operation-logs.edit',$employeeOperationLog->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $employeeOperationLogs->links() !!}
            </div>
        </div>
    </div>
@endsection

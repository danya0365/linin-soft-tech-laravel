@extends('layouts.app')

@section('template_title')
    Employee Working Time
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Employee Working Time') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('employee-working-times.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
										<th>Employee Id</th>
										<th>Working Date</th>
										<th>Time Duration</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employeeWorkingTimes as $employeeWorkingTime)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $employeeWorkingTime->employee_id }}</td>
											<td>{{ $employeeWorkingTime->working_date }}</td>
											<td>{{ $employeeWorkingTime->time_duration }}</td>

                                            <td>
                                                <form action="{{ route('employee-working-times.destroy',$employeeWorkingTime->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('employee-working-times.show',$employeeWorkingTime->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('employee-working-times.edit',$employeeWorkingTime->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $employeeWorkingTimes->links() !!}
            </div>
        </div>
    </div>
@endsection

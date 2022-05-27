@extends('layouts.app')

@section('template_title')
    Department Daily Cost Log
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Department Daily Cost Log') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('department-daily-cost-logs.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
										<th>Department Id</th>
										<th>Daily Date</th>
										<th>Cost</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($departmentDailyCostLogs as $departmentDailyCostLog)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $departmentDailyCostLog->department_id }}</td>
											<td>{{ $departmentDailyCostLog->daily_date }}</td>
											<td>{{ $departmentDailyCostLog->cost }}</td>

                                            <td>
                                                <form action="{{ route('department-daily-cost-logs.destroy',$departmentDailyCostLog->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('department-daily-cost-logs.show',$departmentDailyCostLog->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('department-daily-cost-logs.edit',$departmentDailyCostLog->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $departmentDailyCostLogs->links() !!}
            </div>
        </div>
    </div>
@endsection

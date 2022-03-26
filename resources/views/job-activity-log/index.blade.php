@extends('layouts.app')

@section('template_title')
    Job Activity Log
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Job Activity Log') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('job-activity-logs.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
										<th>Job Id</th>
										<th>Employee Id</th>
										<th>Log Type</th>
										<th>Old Value</th>
										<th>New Value</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jobActivityLogs as $jobActivityLog)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $jobActivityLog->job_id }}</td>
											<td>{{ $jobActivityLog->employee_id }}</td>
											<td>{{ $jobActivityLog->log_type }}</td>
											<td>{{ $jobActivityLog->old_value }}</td>
											<td>{{ $jobActivityLog->new_value }}</td>

                                            <td>
                                                <form action="{{ route('job-activity-logs.destroy',$jobActivityLog->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('job-activity-logs.show',$jobActivityLog->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('job-activity-logs.edit',$jobActivityLog->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $jobActivityLogs->links() !!}
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('template_title')
    Job Group Activity Log
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Job Group Activity Log') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('job-group-activity-logs.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
										<th>Job Group Id</th>
										<th>Employee Id</th>
										<th>Log Type</th>
										<th>Old Value</th>
										<th>New Value</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jobGroupActivityLogs as $jobGroupActivityLog)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $jobGroupActivityLog->job_group_id }}</td>
											<td>{{ $jobGroupActivityLog->employee_id }}</td>
											<td>{{ $jobGroupActivityLog->log_type }}</td>
											<td>{{ $jobGroupActivityLog->old_value }}</td>
											<td>{{ $jobGroupActivityLog->new_value }}</td>

                                            <td>
                                                <form action="{{ route('job-group-activity-logs.destroy',$jobGroupActivityLog->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('job-group-activity-logs.show',$jobGroupActivityLog->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('job-group-activity-logs.edit',$jobGroupActivityLog->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $jobGroupActivityLogs->links() !!}
            </div>
        </div>
    </div>
@endsection

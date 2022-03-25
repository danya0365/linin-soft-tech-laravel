@extends('layouts.app')

@section('template_title')
    Job
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Job') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('jobs.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
										<th>Customer Id</th>
										<th>Employee Id</th>
										<th>Job Type</th>
										<th>Washing Machine Id</th>
										<th>Dryer Machine Id</th>
										<th>Laundry Type Id</th>
										<th>Wet Weight</th>
										<th>Color</th>
										<th>Status</th>
										<th>Tags</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jobs as $job)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $job->job_group_id }}</td>
											<td>{{ $job->customer_id }}</td>
											<td>{{ $job->employee_id }}</td>
											<td>{{ $job->job_type }}</td>
											<td>{{ $job->washing_machine_id }}</td>
											<td>{{ $job->dryer_machine_id }}</td>
											<td>{{ $job->laundry_type_id }}</td>
											<td>{{ $job->wet_weight }}</td>
											<td>{{ $job->color }}</td>
											<td>{{ $job->status }}</td>
											<td>{{ $job->tags }}</td>

                                            <td>
                                                <form action="{{ route('jobs.destroy',$job->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('jobs.show',$job->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('jobs.edit',$job->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $jobs->links() !!}
            </div>
        </div>
    </div>
@endsection

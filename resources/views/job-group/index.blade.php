@extends('layouts.app')

@section('template_title')
    Job Group
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Job Group') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('job-groups.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
										<th>Customer Id</th>
										<th>Employee Id</th>
										<th>Wet Weight</th>
										<th>Dry Weight</th>
										<th>Total Pieces</th>
										<th>Operation Status</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jobGroups as $jobGroup)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $jobGroup->customer_id }}</td>
											<td>{{ $jobGroup->employee_id }}</td>
											<td>{{ $jobGroup->wet_weight }}</td>
											<td>{{ $jobGroup->dry_weight }}</td>
											<td>{{ $jobGroup->total_pieces }}</td>
											<td>{{ $jobGroup->operation_status }}</td>

                                            <td>
                                                <form action="{{ route('job-groups.destroy',$jobGroup->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('job-groups.show',$jobGroup->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('job-groups.edit',$jobGroup->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $jobGroups->links() !!}
            </div>
        </div>
    </div>
@endsection

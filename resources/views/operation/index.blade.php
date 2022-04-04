@extends('layouts.app')

@section('template_title')
    Operation
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Operation') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('operations.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
										<th>Operation Type</th>
										<th>Employee Id</th>
										<th>Customer Id</th>
										<th>Wash Employee Id</th>
										<th>Dry Employee Id</th>
										<th>Iron Employee Id</th>
										<th>Packing Employee Id</th>
										<th>Collect Employee Id</th>
										<th>Job Case</th>
										<th>Washing Machine Id</th>
										<th>Dryer Machine Id</th>
										<th>Total Wet Weight</th>
										<th>Total Dry Weight</th>
										<th>Total Iron Piece</th>
										<th>Total Packing Piece</th>
										<th>Colors</th>
										<th>Search Tags</th>
										<th>Status</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($operations as $operation)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $operation->operation_type }}</td>
											<td>{{ $operation->employee_id }}</td>
											<td>{{ $operation->customer_id }}</td>
											<td>{{ $operation->wash_employee_id }}</td>
											<td>{{ $operation->dry_employee_id }}</td>
											<td>{{ $operation->iron_employee_id }}</td>
											<td>{{ $operation->packing_employee_id }}</td>
											<td>{{ $operation->collect_employee_id }}</td>
											<td>{{ $operation->job_case }}</td>
											<td>{{ $operation->washing_machine_id }}</td>
											<td>{{ $operation->dryer_machine_id }}</td>
											<td>{{ $operation->total_wet_weight }}</td>
											<td>{{ $operation->total_dry_weight }}</td>
											<td>{{ $operation->total_iron_piece }}</td>
											<td>{{ $operation->total_packing_piece }}</td>
											<td>{{ $operation->colors }}</td>
											<td>{{ $operation->search_tags }}</td>
											<td>{{ $operation->status }}</td>

                                            <td>
                                                <form action="{{ route('operations.destroy',$operation->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('operations.show',$operation->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('operations.edit',$operation->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $operations->links() !!}
            </div>
        </div>
    </div>
@endsection

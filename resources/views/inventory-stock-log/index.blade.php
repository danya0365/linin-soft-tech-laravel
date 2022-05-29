@extends('layouts.app')

@section('template_title')
    Inventory Stock Log
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Inventory Stock Log') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('inventory-stock-logs.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
										<th>Inventory Id</th>
										<th>Type</th>
										<th>Quantity</th>
										<th>Cost</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventoryStockLogs as $inventoryStockLog)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $inventoryStockLog->employee_id }}</td>
											<td>{{ $inventoryStockLog->inventory_id }}</td>
											<td>{{ $inventoryStockLog->type }}</td>
											<td>{{ $inventoryStockLog->quantity }}</td>
											<td>{{ $inventoryStockLog->cost }}</td>

                                            <td>
                                                <form action="{{ route('inventory-stock-logs.destroy',$inventoryStockLog->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('inventory-stock-logs.show',$inventoryStockLog->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('inventory-stock-logs.edit',$inventoryStockLog->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $inventoryStockLogs->links() !!}
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('template_title')
    Income
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Income') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('incomes.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
										<th>Type Name</th>
										<th>Table Name</th>
										<th>Table Id</th>
										<th>Amount</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($incomes as $income)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $income->type_name }}</td>
											<td>{{ $income->table_name }}</td>
											<td>{{ $income->table_id }}</td>
											<td>{{ $income->amount }}</td>

                                            <td>
                                                <form action="{{ route('incomes.destroy',$income->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('incomes.show',$income->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('incomes.edit',$income->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $incomes->links() !!}
            </div>
        </div>
    </div>
@endsection

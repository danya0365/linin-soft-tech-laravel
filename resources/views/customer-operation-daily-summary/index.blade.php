@extends('layouts.app')

@section('template_title')
    Customer Operation Daily Summary
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Customer Operation Daily Summary') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('customer-operation-daily-summaries.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
										<th>Customer Id</th>
										<th>Operation Date</th>
										<th>Total Wet Weight</th>
										<th>Total Dry Weight</th>
										<th>Total Iron Piece</th>
										<th>Total Packing Piece</th>
										<th title="คำนวณอัตโนมัติจาก linen_case='edit'">Edit Weight (ระบบ) <i class="fa fa-info-circle text-success"></i></th>
										<th title="กรอกโดย Supervisor ตอนออกบิล">Edit Weight (บันทึกมือ) <i class="fa fa-info-circle text-warning"></i></th>
										<th>Total Collect Weight</th>
										<th>Total Billing Weight</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customerOperationDailySummaries as $customerOperationDailySummary)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
											<td>{{ $customerOperationDailySummary->customer_id }}</td>
											<td>{{ $customerOperationDailySummary->operation_date }}</td>
											<td>{{ $customerOperationDailySummary->total_wet_weight }}</td>
											<td>{{ $customerOperationDailySummary->total_dry_weight }}</td>
											<td>{{ $customerOperationDailySummary->total_iron_piece }}</td>
											<td>{{ $customerOperationDailySummary->total_packing_piece }}</td>
											<td class="text-success">{{ $customerOperationDailySummary->total_edit_collect_weight }}</td>
											<td class="text-warning">{{ $customerOperationDailySummary->total_edit_weight ?? 0 }}</td>
											<td>{{ $customerOperationDailySummary->total_collect_weight }}</td>
											<td>{{ $customerOperationDailySummary->total_billing_weight }}</td>

                                            <td>
                                                <form action="{{ route('customer-operation-daily-summaries.destroy',$customerOperationDailySummary->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('customer-operation-daily-summaries.show',$customerOperationDailySummary->id) }}"><i class="fa fa-fw fa-eye"></i> Show</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('customer-operation-daily-summaries.edit',$customerOperationDailySummary->id) }}"><i class="fa fa-fw fa-edit"></i> Edit</a>
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
                {!! $customerOperationDailySummaries->links() !!}
            </div>
        </div>
    </div>
@endsection

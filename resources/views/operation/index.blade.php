@extends('layouts.app')
@section('template_title')
    Operation
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Operation']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Operation" :createRoute="route('operation.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Operation Type', 'Employee Id', 'Customer Id', 'Wash Employee Id', 'Dry Employee Id', 'Iron Employee Id', 'Packing Employee Id', 'Collect Employee Id', 'Job Case', 'Washing Machine Id', 'Dryer Machine Id', 'Total Wet Weight', 'Total Dry Weight', 'Total Iron Piece', 'Total Packing Piece', 'Colors', 'Search Tags', 'Status', 'Actions']"
            :data="$operations"
            :columns="['operation_type', 'employee_id', 'customer_id', 'wash_employee_id', 'dry_employee_id', 'iron_employee_id', 'packing_employee_id', 'collect_employee_id', 'job_case', 'washing_machine_id', 'dryer_machine_id', 'total_wet_weight', 'total_dry_weight', 'total_iron_piece', 'total_packing_piece', 'colors', 'search_tags', 'status']"
            resource="operation"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $operations->links() }}</div>
</div>
@endsection
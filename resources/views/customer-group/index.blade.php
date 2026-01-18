@extends('layouts.app')

@section('template_title')
    Customer Group
@endsection

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[
        ['label' => 'Admin', 'route' => 'admin'],
        ['label' => 'กลุ่มลูกค้า (Customer Groups)']
    ]" />
    
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header 
                title="Customer Group"
                :createRoute="route('customer-groups.create')"
            />
        </x-slot:header>
        
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        
        <x-crud.data-table 
            :headers="['No', 'Name', 'Actions']"
            :data="$customerGroups"
            :columns="['name']"
            resource="customer-groups"
            :startIndex="$i"
        />
    </x-ui.card>
    
    <div class="mt-6">{{ $customerGroups->links() }}</div>
</div>
@endsection

@extends('layouts.app')
@section('template_title')
    Login History
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => 'admin'],['label' => 'Login History']]" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title="Login History" :createRoute="route('login-histories.create')" />
        </x-slot:header>
        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers="['No', 'Actions']"
            :data="$loginHistories"
            :columns="['']"
            resource="login-histories"
            :startIndex="$i"
        />
    </x-ui.card>
    <div class="mt-6">{{ $loginHistories->links() }}</div>
</div>
@endsection
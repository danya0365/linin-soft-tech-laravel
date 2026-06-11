@extends('layouts.app')
@section('template_title')
    Create Linen Product
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => route('admin')],['label' => 'Linen Product', 'route' => route('linen-products.index')],['label' => 'Create']]" />
    @includeif('partials.errors')
    <x-ui.card>
        <x-slot:header><h2 class="text-xl font-semibold text-gray-900 dark:text-white">Create Linen Product</h2></x-slot:header>
        <form method="POST" action="{{ route('linen-products.store') }}" role="form" enctype="multipart/form-data">
            @csrf
            @include('linen-product.form')
        </form>
    </x-ui.card>
</div>
@endsection
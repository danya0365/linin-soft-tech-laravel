@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <x-number-pad :inputName="'weight1'" :inputValue="12.34">
                น้ำหนักกิโลกรัม
            </x-number-pad>
        </div>
    </div>
</div>

@endsection
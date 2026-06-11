@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-center">
        <div class="w-full m-2">
            <x-number-pad :inputName="'weight1'" :inputValue="12.34">
                น้ำหนักกิโลกรัม
            </x-number-pad>
        </div>
    </div>
</div>

@endsection
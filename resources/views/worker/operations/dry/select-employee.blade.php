@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="เลือกพนักงาน" 
    subtitle="Select Employee" 
    icon="fa-solid fa-users"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'อบ - เลือกพนักงาน']
    ]"
>
    <div class="space-y-6">
        @foreach ($departments as $department)
        <x-worker.card :title="$department['name']">
            <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @foreach ($department['employees'] as $employee)
                <a href="{{ route('worker.operation.dry.set-select-employee', ['employeeId' => $employee['id']]) }}" 
                   class="group flex flex-col items-center p-4 rounded-xl border-2 border-transparent bg-gray-50 dark:bg-gray-800 hover:border-amber-400 hover:bg-white dark:hover:bg-gray-700 shadow-sm hover:shadow-md transition-all duration-200">
                    
                    <div class="w-24 h-24 mb-3 transform group-hover:scale-105 transition-transform duration-200">
                        <x-employee-avatar :photo="$employee['photo']" class="w-full h-full rounded-full shadow-sm" />
                    </div>
                    
                    <span class="text-center font-medium text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400">
                        {{ $employee['name'] }}
                    </span>
                </a>
                @endforeach
            </div>
        </x-worker.card>
        @endforeach
    </div>
</x-worker.page>
@endsection
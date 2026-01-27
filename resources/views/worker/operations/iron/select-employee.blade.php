@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="เลือกพนักงาน" 
    subtitle="Select Employee (Iron Operation)" 
    icon="fa-solid fa-user-tag"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'รีด - เลือกพนักงาน']
    ]"
>
    <div class="space-y-6">
        @foreach ($departments as $department)
        <x-worker.card :title="$department['name']">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @foreach ($department['employees'] as $employee)
                <a href="{{ route('worker.operation.iron.set-select-employee', ['employeeId' => $employee['id']]) }}" 
                   class="group relative flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-transparent hover:border-amber-500 hover:bg-white dark:hover:bg-gray-700 shadow-sm hover:shadow-md transition-all duration-200"
                   style="min-height: 160px">
                    
                    <div class="mb-3 transform group-hover:scale-110 transition-transform duration-200">
                        <x-employee-avatar :photo="$employee['photo']" class="w-20 h-20 rounded-full shadow-sm" />
                    </div>
                    
                    <div class="text-center w-full">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate px-2">
                            {{ $employee['name'] }}
                        </h4>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $employee['code'] }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </x-worker.card>
        @endforeach
    </div>
</x-worker.page>
@endsection
@extends('layouts.worker')

@section('content')

<x-worker.page :breadcrumbs="[['label' => __('เลือกพนักงาน')]]">
    @foreach ($departments as $department)
    <x-worker.card title="{{ $department['name'] }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($department['employees'] as $employee)
            <a href="{{ route('worker.employee.employee-summary', ['employeeId' => $employee['id']]) }}" class="block group">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-500 transition-all duration-200 min-h-[150px]">
                    <div class="flex flex-col items-center justify-center h-full">
                        <div class="w-20 h-20 mb-3">
                            <x-employee-avatar :photo="$employee['photo']" />
                        </div>
                        <div class="text-center text-gray-900 dark:text-white font-medium group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            {{ $employee['name'] }}
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </x-worker.card>
    @endforeach
</x-worker.page>

@endsection
@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="เลือกพนักงาน" 
    subtitle="Select Employee for Wash" 
    icon="fa-solid fa-user-tag"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'ซัก - เลือกพนักงาน']
    ]"
>
    <div class="space-y-6">
        @foreach ($departments as $department)
        <x-worker.card :title="$department['name']">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @foreach ($department['employees'] as $employee)
                <a href="{{ route('worker.operation.wash.set-select-employee', ['employeeId' => $employee['id']]) }}" 
                   class="group relative flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-transparent hover:border-amber-500 hover:bg-white dark:hover:bg-gray-700 shadow-sm hover:shadow-md transition-all duration-200"
                   style="min-height: 160px">
                    
                    <div class="mb-3 transform group-hover:scale-105 transition-transform duration-200">
                        <div class="w-20 h-20">
                            <x-employee-avatar :photo="$employee['photo']" class="w-full h-full rounded-full shadow-sm" />
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                            {{ $employee['name'] }}
                        </h4>
                    </div>
                    
                    {{-- Active Indicator (Optional, if needed for visual feedback) --}}
                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fa fa-check-circle text-amber-500"></i>
                    </div>
                </a>
                @endforeach
            </div>
        </x-worker.card>
        @endforeach
    </div>
</x-worker.page>
@endsection
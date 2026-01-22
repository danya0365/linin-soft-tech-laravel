@extends('layouts.worker')

@section('content')
<x-menu.page-layout 
    title="{{ __('เลือกพนักงาน') }}"
    subtitle="ดูสรุปข้อมูลพนักงานแต่ละคน"
    icon="fa-users"
    icon-color="text-cyan-600"
    :breadcrumbs="[
        ['label' => 'Worker', 'url' => route('worker')],
        ['label' => 'พนักงาน']
    ]"
>
    @foreach ($departments as $department)
    <x-menu.section 
        title="{{ $department['name'] }}"
        icon="fa-building"
        icon-color="text-gray-500"
    >
        @foreach ($department['employees'] as $employee)
        <a href="{{ route('worker.employee.employee-summary', ['employeeId' => $employee['id']]) }}" class="group block">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 dark:from-gray-700 dark:to-gray-800 p-4 sm:p-6 h-44 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 border border-gray-200 dark:border-gray-600">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-500/10 rounded-full blur-xl"></div>
                
                <div class="relative flex flex-col items-center justify-center h-full">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 mb-3 rounded-full overflow-hidden border-2 border-white shadow-md">
                        <x-employee-avatar :photo="$employee['photo']" />
                    </div>
                    <div class="text-center text-gray-900 dark:text-white font-semibold group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ $employee['name'] }}
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </x-menu.section>
    @endforeach
</x-menu.page-layout>
@endsection
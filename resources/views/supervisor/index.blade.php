@extends('layouts.supervisor')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                        <i class="fa fa-home mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                        <span class="text-sm font-semibold text-gray-700 dark:text-white">{{ __('Supervisor') }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fa fa-user-tie mr-3 text-indigo-600"></i>{{ __('Supervisor Menu') }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400">จัดการข้อมูลลูกค้า แผนก และรายงานสถิติ</p>
        </div>

        <!-- Menu Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Customer -->
            <a href="{{ route('supervisor.customer') }}" class="group">
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-6 h-44 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
                    <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-hospital text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold">{{ __('ลูกค้า') }}</h3>
                        <p class="text-sm text-emerald-100">Customer</p>
                    </div>
                </div>
            </a>

            <!-- Department -->
            <a href="{{ route('supervisor.department') }}" class="group">
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 p-6 h-44 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
                    <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-users-rectangle text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold">{{ __('แผนก') }}</h3>
                        <p class="text-sm text-cyan-100">Department</p>
                    </div>
                </div>
            </a>

            <!-- Report -->
            <a href="{{ route('supervisor.report') }}" class="group">
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 p-6 h-44 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
                    <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-chart-pie text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold">{{ __('รายงานสถิติ') }}</h3>
                        <p class="text-sm text-amber-100">Report</p>
                    </div>
                </div>
            </a>

        </div>
    </div>
</div>
@endsection
@extends('layouts.manager')

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
                        <span class="text-sm font-semibold text-gray-700 dark:text-white">{{ __('Manager') }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fa fa-briefcase mr-3 text-indigo-600"></i>{{ __('Manager Menu') }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400">ดูรายงานสถิติและวิเคราะห์ข้อมูล</p>
        </div>

        <!-- Menu Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Report -->
            <a href="{{ route('manager.report') }}" class="group">
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-800 p-8 h-52 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 bg-white/20 rounded-full text-xs text-white font-medium">
                            <i class="fa fa-star mr-1"></i> Overview
                        </span>
                    </div>
                    <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                        <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-chart-pie text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold">{{ __('รายงานสถิติ') }}</h3>
                        <p class="text-sm text-indigo-200 mt-1">Report Overview</p>
                    </div>
                </div>
            </a>

            <!-- Day Report -->
            <a href="{{ route('manager.report.filter') }}" class="group">
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-600 to-violet-800 p-8 h-52 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 bg-white/20 rounded-full text-xs text-white font-medium">
                            <i class="fa fa-calendar mr-1"></i> Filter
                        </span>
                    </div>
                    <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                        <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-calendar-days text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold">{{ __('สถิติตามวันที่') }}</h3>
                        <p class="text-sm text-purple-200 mt-1">Day Report</p>
                    </div>
                </div>
            </a>

        </div>
    </div>
</div>
@endsection
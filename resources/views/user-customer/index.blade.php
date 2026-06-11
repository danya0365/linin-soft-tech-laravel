@extends('layouts.user-customer')

@section('content')

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Breadcrumbs --}}
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-200">
                    <i class="fa-solid fa-home mr-2"></i>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">User Customer</span>
                </div>
            </li>
        </ol>
    </nav>
    
    <div class="flex justify-center">
        <div class="w-full max-w-4xl">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 transaction-all duration-300 hover:shadow-2xl">
                {{-- Header with gradient background --}}
                <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white text-center py-12 px-6 relative overflow-hidden">
                    {{-- Decorative circles --}}
                    <div class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>

                    <div class="mb-6 relative z-10 animate-bounce-slow">
                        <i class="fa-solid fa-tools text-7xl drop-shadow-xl"></i>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-extrabold mb-2 relative z-10 drop-shadow-md">🚧 Under Construction 🚧</h2>
                    <p class="text-indigo-100 text-lg relative z-10">กำลังพัฒนา</p>
                </div>
                
                <div class="p-8 md:p-12 text-center space-y-8">
                    {{-- Main message --}}
                    <div>
                        <h4 class="text-2xl font-bold text-gray-800 dark:text-white mb-4 flex items-center justify-center gap-3">
                            <i class="fa-solid fa-gear fa-spin text-indigo-500"></i>
                            ฟีเจอร์นี้อยู่ระหว่างการพัฒนา
                        </h4>
                        <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed max-w-2xl mx-auto">
                            เรากำลังพัฒนาระบบ User Customer ในเฟสถัดไป<br>
                            กรุณารอติดตามการอัปเดตเร็วๆ นี้
                        </p>
                    </div>
                    
                    {{-- Feature preview --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-6 rounded-2xl bg-slate-50 dark:bg-gray-700/50 hover:bg-white hover:shadow-lg dark:hover:bg-gray-700 transition-all duration-300 border border-slate-100 dark:border-gray-600 group">
                            <i class="fa-solid fa-chart-line text-4xl text-blue-500 mb-4 group-hover:scale-110 transition-transform duration-300"></i>
                            <p class="font-medium text-gray-600 dark:text-gray-300">ดูรายงานสรุป</p>
                        </div>
                        <div class="p-6 rounded-2xl bg-slate-50 dark:bg-gray-700/50 hover:bg-white hover:shadow-lg dark:hover:bg-gray-700 transition-all duration-300 border border-slate-100 dark:border-gray-600 group">
                            <i class="fa-solid fa-file-invoice text-4xl text-green-500 mb-4 group-hover:scale-110 transition-transform duration-300"></i>
                            <p class="font-medium text-gray-600 dark:text-gray-300">ตรวจสอบบิล</p>
                        </div>
                        <div class="p-6 rounded-2xl bg-slate-50 dark:bg-gray-700/50 hover:bg-white hover:shadow-lg dark:hover:bg-gray-700 transition-all duration-300 border border-slate-100 dark:border-gray-600 group">
                            <i class="fa-solid fa-history text-4xl text-amber-500 mb-4 group-hover:scale-110 transition-transform duration-300"></i>
                            <p class="font-medium text-gray-600 dark:text-gray-300">ประวัติงาน</p>
                        </div>
                    </div>
                    
                    {{-- Status badge --}}
                    <div>
                        <span class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-gradient-to-r from-fuchsia-100 to-pink-100 dark:from-fuchsia-900/30 dark:to-pink-900/30 text-fuchsia-700 dark:text-fuchsia-300 font-semibold text-sm shadow-sm ring-1 ring-fuchsia-200 dark:ring-fuchsia-800">
                            <i class="fa-solid fa-clock animate-pulse"></i> Coming Soon - Phase 2
                        </span>
                    </div>
                    
                    {{-- Back button --}}
                    <div class="pt-4">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-300 font-bold rounded-full border-2 border-indigo-100 dark:border-gray-600 hover:border-indigo-500 dark:hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-600 transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-1">
                            <i class="fa-solid fa-home"></i> กลับหน้าหลัก
                        </a>
                    </div>
                </div>
                
                {{-- Footer --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 text-center py-4 border-t border-gray-100 dark:border-gray-700">
                    <small class="text-gray-500 dark:text-gray-400 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-info-circle"></i>
                        หากมีข้อสงสัย กรุณาติดต่อทีมงาน LinenSoftTech
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .animate-bounce-slow {
        animation: bounce-slow 2s infinite ease-in-out;
    }
</style>
@endsection
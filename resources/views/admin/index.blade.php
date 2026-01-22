@extends('layouts.admin')

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
                        <span class="text-sm font-semibold text-gray-700 dark:text-white">Admin</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fa fa-cog mr-3 text-slate-600"></i>{{ __('Admin Menu') }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400">จัดการข้อมูลหลักของระบบ</p>
        </div>

        <!-- Section: Users & Customers -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center">
                <i class="fa fa-users mr-2 text-blue-500"></i> ผู้ใช้งาน & ลูกค้า
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                
                <!-- Users -->
                <a href="{{ route('users.index') }}" class="group">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 p-5 h-36 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-white/10 rounded-full blur-lg"></div>
                        <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-people-group text-2xl"></i>
                            </div>
                            <h3 class="font-bold">{{ __('ไอดีล็อกอิน') }}</h3>
                            <p class="text-xs text-blue-200">Users</p>
                        </div>
                    </div>
                </a>

                <!-- Customer Groups -->
                <a href="{{ route('customer-groups.index') }}" class="group">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 p-5 h-36 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-white/10 rounded-full blur-lg"></div>
                        <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-building-user text-2xl"></i>
                            </div>
                            <h3 class="font-bold">{{ __('กลุ่มลูกค้า') }}</h3>
                            <p class="text-xs text-emerald-200">Customer Groups</p>
                        </div>
                    </div>
                </a>

                <!-- Customers -->
                <a href="{{ route('customers.index') }}" class="group">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 p-5 h-36 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-white/10 rounded-full blur-lg"></div>
                        <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-hospital text-2xl"></i>
                            </div>
                            <h3 class="font-bold">{{ __('ลูกค้า') }}</h3>
                            <p class="text-xs text-cyan-200">Customers</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>

        <!-- Section: Employees -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center">
                <i class="fa fa-id-badge mr-2 text-orange-500"></i> พนักงาน
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                <!-- Departments -->
                <a href="{{ route('departments.index') }}" class="group">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 p-5 h-36 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-white/10 rounded-full blur-lg"></div>
                        <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-people-roof text-2xl"></i>
                            </div>
                            <h3 class="font-bold">{{ __('แผนกพนักงาน') }}</h3>
                            <p class="text-xs text-amber-100">Departments</p>
                        </div>
                    </div>
                </a>

                <!-- Employees -->
                <a href="{{ route('employees.index') }}" class="group">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-orange-500 to-red-500 p-5 h-36 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-white/10 rounded-full blur-lg"></div>
                        <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-person-digging text-2xl"></i>
                            </div>
                            <h3 class="font-bold">{{ __('พนักงาน') }}</h3>
                            <p class="text-xs text-orange-200">Employees</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>

        <!-- Section: Products -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center">
                <i class="fa fa-shirt mr-2 text-violet-500"></i> สินค้า
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                <!-- Linen Types -->
                <a href="{{ route('linen-types.index') }}" class="group">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 p-5 h-36 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-white/10 rounded-full blur-lg"></div>
                        <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-layer-group text-2xl"></i>
                            </div>
                            <h3 class="font-bold">{{ __('ชนิดผ้า') }}</h3>
                            <p class="text-xs text-violet-200">Linen Types</p>
                        </div>
                    </div>
                </a>

                <!-- Linen Products -->
                <a href="{{ route('linen-products.index') }}" class="group">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 p-5 h-36 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-white/10 rounded-full blur-lg"></div>
                        <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-shirt text-2xl"></i>
                            </div>
                            <h3 class="font-bold">{{ __('ผ้า') }}</h3>
                            <p class="text-xs text-purple-200">Linen Products</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>

        <!-- Section: Machines & Vehicles -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center">
                <i class="fa fa-cogs mr-2 text-slate-500"></i> เครื่องจักร & ยานพาหนะ
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                <!-- Washing Machines -->
                <a href="{{ route('washing-machines.index') }}" class="group">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 p-5 h-36 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-white/10 rounded-full blur-lg"></div>
                        <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-soap text-2xl"></i>
                            </div>
                            <h3 class="font-bold">{{ __('เครื่องซักผ้า') }}</h3>
                            <p class="text-xs text-sky-200">Washing Machines</p>
                        </div>
                    </div>
                </a>

                <!-- Dryer Machines -->
                <a href="{{ route('dryer-machines.index') }}" class="group">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-rose-500 to-red-600 p-5 h-36 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-white/10 rounded-full blur-lg"></div>
                        <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-fire text-2xl"></i>
                            </div>
                            <h3 class="font-bold">{{ __('เครื่องอบผ้า') }}</h3>
                            <p class="text-xs text-rose-200">Dryer Machines</p>
                        </div>
                    </div>
                </a>

                <!-- Trucks -->
                <a href="{{ route('trucks.index') }}" class="group">
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-500 to-gray-700 p-5 h-36 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-white/10 rounded-full blur-lg"></div>
                        <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-truck text-2xl"></i>
                            </div>
                            <h3 class="font-bold">{{ __('รถบรรทุก') }}</h3>
                            <p class="text-xs text-slate-300">Trucks</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>

    </div>
</div>
@endsection
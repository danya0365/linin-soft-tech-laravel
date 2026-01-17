@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

    <div class="max-w-6xl w-full">
        <div class="grid lg:grid-cols-2 gap-8 items-center">
            
            {{-- Left Column - Branding & Graphics --}}
            <div class="hidden lg:flex flex-col justify-center space-y-8 p-8">
                {{-- Logo --}}
                <div class="flex items-center justify-center p-8 bg-white/60 dark:bg-gray-800/60 backdrop-blur-md rounded-3xl shadow-xl transition-colors duration-200">
                    <img src="{{ asset('assets/logo-1.png') }}" 
                         alt="LinenSoftTech Logo" 
                         class="max-w-xs w-full h-auto drop-shadow-2xl hover:scale-105 transition-transform duration-500" />
                </div>
                
                {{-- Welcome Text --}}
                <div class="text-center space-y-4">
                    <h2 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                        Welcome Back!
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300 font-medium transition-colors duration-200">
                        เข้าสู่ระบบเพื่อจัดการธุรกิจของคุณได้อย่างมีประสิทธิภาพ
                    </p>
                </div>

                {{-- Feature Pills --}}
                <div class="flex flex-wrap gap-3 justify-center">
                    <span class="px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-full text-sm font-medium shadow-lg hover:shadow-xl transition-shadow duration-300">
                        🚀 Fast & Secure
                    </span>
                    <span class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full text-sm font-medium shadow-lg hover:shadow-xl transition-shadow duration-300">
                        ✨ Easy to Use
                    </span>
                    <span class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full text-sm font-medium shadow-lg hover:shadow-xl transition-shadow duration-300">
                        💼 Professional
                    </span>
                </div>
            </div>

            {{-- Right Column - Login Form --}}
            <div class="w-full">
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl p-8 sm:p-10 border border-white/20 dark:border-gray-700/20 hover:shadow-3xl transition-all duration-500">
                    
                    {{-- Mobile Logo --}}
                    <div class="lg:hidden flex justify-center mb-8">
                        <img src="{{ asset('assets/logo.png') }}" 
                             alt="LinenSoftTech Logo" 
                             class="max-w-[180px] h-auto drop-shadow-lg" />
                    </div>

                    {{-- Header --}}
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-200">
                            Sign In
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 transition-colors duration-200">
                            เข้าสู่ระบบด้วยบัญชีของคุณ
                        </p>
                    </div>

                    {{-- Login Form --}}
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        {{-- Email Input Group --}}
                        <div class="relative group">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                {{ __('Email Address') }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 group-focus-within:text-indigo-600 dark:group-focus-within:text-indigo-400 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                                <input id="email" 
                                       type="email" 
                                       name="email" 
                                       value="{{ env('APP_ENV', '') == 'local' ? 'admin.linensofttech@gmail.com' : old('email') }}"
                                       required 
                                       autocomplete="email" 
                                       autofocus
                                       class="block w-full pl-12 pr-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl 
                                              text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 
                                              focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent 
                                              transition-all duration-200 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm
                                              @error('email') border-red-500 ring-2 ring-red-200 @enderror"
                                       placeholder="your.email@example.com">
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1 animate-pulse">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Password Input Group --}}
                        <div class="relative group">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
                                {{ __('Password') }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 group-focus-within:text-indigo-600 dark:group-focus-within:text-indigo-400 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input id="password" 
                                       type="password" 
                                       name="password" 
                                       value="{{ env('APP_ENV', '') == 'local' ? '12345678' : '' }}"
                                       required 
                                       autocomplete="current-password"
                                       class="block w-full pl-12 pr-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl 
                                              text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 
                                              focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent 
                                              transition-all duration-200 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm
                                              @error('password') border-red-500 ring-2 ring-red-200 @enderror"
                                       placeholder="••••••••">
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1 animate-pulse">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember" 
                                       name="remember" 
                                       type="checkbox" 
                                       {{ old('remember') ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                                <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" 
                                   class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors duration-200">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="space-y-4">
                            <button type="submit" 
                                    class="w-full flex items-center justify-center gap-2 py-3.5 px-6 
                                           bg-gradient-to-r from-indigo-600 to-purple-600 
                                           hover:from-indigo-700 hover:to-purple-700 
                                           text-white font-semibold rounded-xl shadow-lg 
                                           hover:shadow-xl transform hover:-translate-y-0.5 
                                           transition-all duration-200 
                                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                {{ __('Login') }}
                            </button>

                            {{-- Divider --}}
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-4 bg-white/80 dark:bg-gray-800/80 text-gray-500 dark:text-gray-400 transition-colors duration-200">
                                        or continue with
                                    </span>
                                </div>
                            </div>

                            <!-- Social Login Placeholder (Optional) -->
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" 
                                        disabled
                                        class="flex items-center justify-center gap-2 py-2.5 px-4 border border-gray-300 dark:border-gray-600 rounded-xl 
                                               text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 
                                               transition-all duration-200 opacity-50 cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/>
                                    </svg>
                                    Google
                                </button>
                                <button type="button" 
                                        disabled
                                        class="flex items-center justify-center gap-2 py-2.5 px-4 border border-gray-300 dark:border-gray-600 rounded-xl 
                                               text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 
                                               transition-all duration-200 opacity-50 cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                    </svg>
                                    GitHub
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Background Animation -->
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
</style>
@endsection

@extends('layouts.app')

@section('content')
@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        {{-- Logo --}}
        <div class="flex justify-center mb-8">
            <img src="{{ asset('assets/logo-1.png') }}" 
                 alt="LinenSoftTech Logo" 
                 class="h-16 w-auto drop-shadow-lg hover:scale-105 transition-transform duration-300" />
        </div>

        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl p-8 sm:p-10 border border-white/20 dark:border-gray-700/20">
            
            {{-- Header --}}
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ __('Reset Password') }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    ตั้งค่ารหัสผ่านใหม่ของคุณ
                </p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Email Address') }}
                    </label>
                    <input id="email" type="email" 
                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl 
                                  text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 
                                  focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent 
                                  bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm transition-all duration-200
                                  @error('email') border-red-500 ring-2 ring-red-200 @enderror" 
                           name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1 animate-pulse">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Password') }}
                    </label>
                    <input id="password" type="password" 
                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl 
                                  text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 
                                  focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent 
                                  bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm transition-all duration-200
                                  @error('password') border-red-500 ring-2 ring-red-200 @enderror" 
                           name="password" required autocomplete="new-password">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1 animate-pulse">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password-confirm" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Confirm Password') }}
                    </label>
                    <input id="password-confirm" type="password" 
                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl 
                                  text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 
                                  focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent 
                                  bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm transition-all duration-200" 
                           name="password_confirmation" required autocomplete="new-password">
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 py-3.5 px-6 
                                   bg-gradient-to-r from-indigo-600 to-purple-600 
                                   hover:from-indigo-700 hover:to-purple-700 
                                   text-white font-semibold rounded-xl shadow-lg 
                                   hover:shadow-xl transform hover:-translate-y-0.5 
                                   transition-all duration-200 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@endsection

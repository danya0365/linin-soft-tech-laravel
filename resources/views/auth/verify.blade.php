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
                    {{ __('Verify Your Email Address') }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                </p>
            </div>

            <div class="text-center space-y-4">
                @if (session('resent'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl flex items-center gap-3" role="alert">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </div>
                @endif

                <p class="text-gray-600 dark:text-gray-300">
                    {{ __('If you did not receive the email') }},
                </p>

                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center justify-center gap-2 py-2 px-4 
                                   bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 
                                   font-semibold rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 
                                   transition-colors duration-200">
                        {{ __('click here to request another') }}
                    </button>.
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

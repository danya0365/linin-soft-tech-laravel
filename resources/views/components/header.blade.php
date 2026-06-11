<nav class="bg-white/80 dark:bg-dark-card/80 backdrop-blur-md shadow-lg border-b border-gray-200 dark:border-dark-border sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            
            <!-- Logo/Brand -->
            <div class="flex-shrink-0">
                <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-md group-hover:shadow-lg transition-all duration-200">
                        <span class="text-white font-bold text-xl">L</span>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent hidden sm:block">
                        {{ config('app.name', 'Laravel') }}
                    </span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-1">
                @auth
                    <a href="{{ route('worker') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-all duration-200">
                        {{ __('Worker') }}
                    </a>
                    <a href="{{ route('supervisor') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-all duration-200">
                        {{ __('Supervisor') }}
                    </a>
                    <a href="{{ route('manager') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-all duration-200">
                        {{ __('Manager') }}
                    </a>
                    <a href="{{ route('admin') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-all duration-200">
                        {{ __('Admin') }}
                    </a>

                    <!-- User Dropdown -->
                    <div class="relative ml-3" x-data="{ open: false }">
                        <button @click="open = !open" 
                                @click.away="open = false"
                                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-md hover:shadow-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ Auth::user()->name }}
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 overflow-hidden">
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="block px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-red-500 hover:to-pink-500 hover:text-white transition-all duration-200">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    {{ __('Logout') }}
                                </div>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" 
                           class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-all duration-200">
                            {{ __('Login') }}
                        </a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" 
                           class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-md hover:shadow-lg transition-all duration-200">
                            {{ __('Register') }}
                        </a>
                    @endif
                @endauth

                <!-- Theme Toggle - Always visible -->
                <div class="ml-2">
                    <x-theme-toggle></x-theme-toggle>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center gap-2">
                <!-- Theme Toggle for Mobile -->
                <x-theme-toggle></x-theme-toggle>
                
                <button type="button" 
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="inline-flex items-center justify-center p-2 rounded-lg text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-all duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="md:hidden border-t border-gray-200 dark:border-dark-border bg-white/95 dark:bg-dark-card/95 backdrop-blur-md transition-colors duration-200">
        <div class="px-4 py-3 space-y-1">
            @auth
                <a href="{{ route('worker') }}" 
                   class="block px-4 py-3 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-all duration-200">
                    {{ __('Worker') }}
                </a>
                <a href="{{ route('supervisor') }}" 
                   class="block px-4 py-3 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-all duration-200">
                    {{ __('Supervisor') }}
                </a>
                <a href="{{ route('manager') }}" 
                   class="block px-4 py-3 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-all duration-200">
                    {{ __('Manager') }}
                </a>
                <a href="{{ route('admin') }}" 
                   class="block px-4 py-3 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-all duration-200">
                    {{ __('Admin') }}
                </a>
                
                <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                
                <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ Auth::user()->name }}
                </div>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                   class="block px-4 py-3 rounded-lg text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all duration-200">
                    {{ __('Logout') }}
                </a>
                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" 
                       class="block px-4 py-3 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-all duration-200">
                        {{ __('Login') }}
                    </a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" 
                       class="block px-4 py-3 rounded-lg text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 transition-all duration-200">
                        {{ __('Register') }}
                    </a>
                @endif
            @endauth
        </div>
    </div>
</nav>

<!-- Alpine.js for interactive components -->
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('navigation', () => ({
            mobileMenuOpen: false
        }))
    })
</script>
{{--
    Card Component
    
    Usage:
    <x-ui.card>
        <x-slot:header>
            <h2 class="text-xl font-bold">Card Title</h2>
        </x-slot:header>
        
        Card content goes here
        
        <x-slot:footer>
            <button>Action</button>
        </x-slot:footer>
    </x-ui.card>
    
    Props:
    - hover: Enable hover effect (default: false)
    - padding: Custom padding class (default: 'p-6')
--}}

@props([
    'hover' => false,
    'padding' => null
])

<div {{ $attributes->merge(['class' => 
    'bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-all duration-200 overflow-hidden' .
    ($hover ? ' hover:shadow-2xl hover:-translate-y-1' : '')
]) }}>
    
    @isset($header)
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            {{ $header }}
        </div>
    @endisset
    
    <div class="{{ $padding ?? 'p-6' }}">
        {{ $slot }}
    </div>
    
    @isset($footer)
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            {{ $footer }}
        </div>
    @endisset
</div>

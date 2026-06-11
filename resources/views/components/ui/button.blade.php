{{--
    Button Component
    
    Usage:
    <x-ui.button variant="primary" href="{{ route('...') }}">
        Click Me
    </x-ui.button>
    
    <x-ui.button variant="danger" type="submit">
        Delete
    </x-ui.button>
    
    Props:
    - variant: primary|secondary|success|danger|warning|info|outline (default: primary)
    - size: sm|md|lg (default: md)
    - href: Link URL (creates <a> tag)
    - type: button|submit|reset (for <button> tag)
    - icon: Icon class (optional)
    - iconPosition: left|right (default: left)
--}}

@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'icon' => null,
    'iconPosition' => 'left'
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2.5 text-base',
        'lg' => 'px-6 py-3 text-lg',
    ];
    
    $variantClasses = [
        'primary' => 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus:ring-indigo-500 dark:focus:ring-indigo-400',
        'secondary' => 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 focus:ring-gray-500',
        'success' => 'bg-green-600 text-white hover:bg-green-700 shadow-lg hover:shadow-xl focus:ring-green-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 shadow-lg hover:shadow-xl focus:ring-red-500',
        'warning' => 'bg-yellow-500 text-white hover:bg-yellow-600 shadow-lg hover:shadow-xl focus:ring-yellow-500',
        'info' => 'bg-cyan-600 text-white hover:bg-cyan-700 shadow-lg hover:shadow-xl focus:ring-cyan-500',
        'outline' => 'border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-gray-500',
    ];
    
    $classes = $baseClasses . ' ' . $sizeClasses[$size] . ' ' . $variantClasses[$variant];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <i class="{{ $icon }}"></i>
        @endif
        
        {{ $slot }}
        
        @if($icon && $iconPosition === 'right')
            <i class="{{ $icon }}"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <i class="{{ $icon }}"></i>
        @endif
        
        {{ $slot }}
        
        @if($icon && $iconPosition === 'right')
            <i class="{{ $icon }}"></i>
        @endif
    </button>
@endif

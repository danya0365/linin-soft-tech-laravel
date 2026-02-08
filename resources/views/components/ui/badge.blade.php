{{--
    Badge Component
    
    Usage:
    <x-ui.badge variant="primary">New</x-ui.badge>
    <x-ui.badge variant="success">Active</x-ui.badge>
    <x-ui.badge variant="danger" size="lg">Error</x-ui.badge>
    
    Props:
    - variant: primary|secondary|success|danger|warning|info (default: primary)
    - size: sm|md|lg (default: md)
    - rounded: true|false (default: true for pill shape)
--}}

@props([
    'variant' => 'primary',
    'size' => 'md',
    'rounded' => true
])

@php
    $baseClasses = 'inline-flex items-center gap-1 font-semibold transition-colors duration-200';
    
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-0.5 text-xs',
        'lg' => 'px-3 py-1 text-sm',
    ];
    
    $variantClasses = [
        'primary' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300',
        'secondary' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'success' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
        'danger' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
        'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
        'info' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/50 dark:text-cyan-300',
    ];
    
    $roundedClass = $rounded ? 'rounded-full' : 'rounded';
    
    $classes = $baseClasses . ' ' . $sizeClasses[$size] . ' ' . $variantClasses[$variant] . ' ' . $roundedClass;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>

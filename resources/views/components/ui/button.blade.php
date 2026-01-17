@props([
    'variant' => 'primary', // primary, secondary, success, danger, ghost
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconOnly' => false,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-sm rounded-md',
    'md' => 'px-4 py-2 text-base rounded-lg',
    'lg' => 'px-6 py-3 text-lg rounded-xl',
];

$variantClasses = [
    'primary' => 'bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white shadow-lg hover:shadow-xl focus:ring-purple-500 dark:from-purple-500 dark:to-indigo-500',
    'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100 focus:ring-gray-500',
    'success' => 'bg-green-600 hover:bg-green-700 text-white dark:bg-green-500 dark:hover:bg-green-600 focus:ring-green-500',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white dark:bg-red-500 dark:hover:bg-red-600 focus:ring-red-500',
    'ghost' => 'bg-transparent hover:bg-gray-100 text-gray-700 dark:hover:bg-gray-800 dark:text-gray-300 focus:ring-gray-500',
];

$iconOnlyClasses = $iconOnly ? 'p-2' : '';

$classes = trim("$baseClasses {$sizeClasses[$size]} {$variantClasses[$variant]} $iconOnlyClasses");
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && !$iconOnly)
            <span class="mr-2">{!! $icon !!}</span>
        @elseif($icon)
            {!! $icon !!}
        @endif
        
        @if(!$iconOnly)
            {{ $slot }}
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && !$iconOnly)
            <span class="mr-2">{!! $icon !!}</span>
        @elseif($icon)
            {!! $icon !!}
        @endif
        
        @if(!$iconOnly)
            {{ $slot }}
        @endif
    </button>
@endif

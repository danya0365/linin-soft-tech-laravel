{{--
    Alert Component
    
    Usage:
    <x-ui.alert variant="success">
        Operation completed successfully!
    </x-ui.alert>
    
    <x-ui.alert variant="danger" dismissible="true">
        An error occurred!
    </x-ui.alert>
    
    Props:
    - variant: success|danger|warning|info (default: info)
    - dismissible: true|false (default: false)
    - icon: Custom icon class (optional, uses default icons per variant)
--}}

@props([
    'variant' => 'info',
    'dismissible' => false,
    'icon' => null
])

@php
    $baseClasses = 'p-4 rounded-lg border flex items-start gap-3';
    
    $variantConfig = [
        'success' => [
            'classes' => 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300',
            'icon' => 'fa fa-check-circle'
        ],
        'danger' => [
            'classes' => 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300',
            'icon' => 'fa fa-exclamation-circle'
        ],
        'warning' => [
            'classes' => 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-300',
            'icon' => 'fa fa-exclamation-triangle'
        ],
        'info' => [
            'classes' => 'bg-cyan-50 border-cyan-200 text-cyan-800 dark:bg-cyan-900/20 dark:border-cyan-800 dark:text-cyan-300',
            'icon' => 'fa fa-info-circle'
        ],
        'primary' => [
            'classes' => 'bg-indigo-50 border-indigo-200 text-indigo-800 dark:bg-indigo-900/20 dark:border-indigo-800 dark:text-indigo-300',
            'icon' => 'fa fa-info-circle'
        ],
    ];
    
    $config = $variantConfig[$variant];
    $classes = $baseClasses . ' ' . $config['classes'];
    $iconClass = $icon ?? $config['icon'];
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} @if($dismissible) x-data="{ show: true }" x-show="show" x-transition @endif>
    <div class="flex-shrink-0">
        <i class="{{ $iconClass }} text-lg"></i>
    </div>
    
    <div class="flex-1">
        {{ $slot }}
    </div>
    
    @if($dismissible)
        <button @click="show = false" class="flex-shrink-0 ml-auto text-current opacity-50 hover:opacity-100 transition-opacity">
            <i class="fa fa-times"></i>
        </button>
    @endif
</div>

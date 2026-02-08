@props([
    'title',
    'value',
    'unit' => null,
    'color' => 'indigo', // indigo, cyan, amber, red, emerald, gray
    'icon' => null,
    'bgClass' => 'bg-white dark:bg-gray-800'
])

@php
    $colors = [
        'primary' => 'text-indigo-600 dark:text-indigo-400',
        'indigo' => 'text-indigo-600 dark:text-indigo-400',
        'info' => 'text-cyan-600 dark:text-cyan-400',
        'cyan' => 'text-cyan-600 dark:text-cyan-400',
        'warning' => 'text-amber-500 dark:text-amber-400',
        'amber' => 'text-amber-500 dark:text-amber-400',
        'danger' => 'text-red-600 dark:text-red-400',
        'red' => 'text-red-600 dark:text-red-400',
        'success' => 'text-emerald-600 dark:text-emerald-400',
        'emerald' => 'text-emerald-600 dark:text-emerald-400',
        'secondary' => 'text-gray-600 dark:text-gray-400',
        'gray' => 'text-gray-600 dark:text-gray-400',
    ];

    $textColor = $colors[$color] ?? $colors['indigo'];
@endphp

<div class="{{ $bgClass }} rounded-xl border border-gray-200 dark:border-gray-700 p-4 h-full shadow-sm hover:shadow-md transition-shadow duration-200">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $title }}</p>
            <h3 class="text-2xl font-bold {{ $textColor }}">{{ $value }}</h3>
            @if($unit)
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $unit }}</p>
            @endif
        </div>
        @if($icon)
            <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <i class="fa {{ $icon }} {{ $textColor }}"></i>
            </div>
        @endif
    </div>
</div>

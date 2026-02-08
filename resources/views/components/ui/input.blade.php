@props([
    'label' => null,
    'error' => null,
    'helper' => null,
    'icon' => null,
    'type' => 'text',
    'required' => false,
])

@php
$baseClasses = 'w-full px-4 py-2.5 rounded-lg border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-400';

$errorClasses = $error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600';

$iconPadding = $icon ? 'pl-10' : '';

$classes = trim("$baseClasses $errorClasses $iconPadding");
@endphp

<div {{ $attributes->only('class') }}>
    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-400 dark:text-gray-500">{!! $icon !!}</span>
            </div>
        @endif

        <input 
            type="{{ $type }}"
            {{ $attributes->except(['class'])->merge(['class' => $classes]) }}
        >
    </div>

    @if($error)
        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($helper && !$error)
        <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">{{ $helper }}</p>
    @endif
</div>

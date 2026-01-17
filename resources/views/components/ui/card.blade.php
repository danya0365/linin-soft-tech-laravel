@props([
    'variant' => 'default', // default, glass, bordered
    'padding' => 'md', // none, sm, md, lg
])

@php
$baseClasses = 'rounded-xl transition-all duration-200';

$variantClasses = [
    'default' => 'bg-white dark:bg-dark-card shadow-lg dark:shadow-2xl',
    'glass' => 'bg-white/80 dark:bg-dark-card/80 backdrop-blur-lg shadow-xl',
    'bordered' => 'bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border',
];

$paddingClasses = [
    'none' => '',
    'sm' => 'p-4',
    'md' => 'p-6',
    'lg' => 'p-8',
];

$classes = trim("$baseClasses {$variantClasses[$variant]} {$paddingClasses[$padding]}");
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>

@props([
    'columns' => 3
])

@php
    $gridClass = match((int)$columns) {
        2 => 'grid-cols-2',
        3 => 'grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
        default => 'grid-cols-2 lg:grid-cols-3'
    };
@endphp

<div class="grid {{ $gridClass }} gap-4 sm:gap-6">
    {{ $slot }}
</div>

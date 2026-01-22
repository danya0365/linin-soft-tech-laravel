{{-- Worker Action Card Component (TailwindCSS) --}}
{{-- Usage: <x-worker.action-card href="..." icon="fa-solid fa-..." title="Label" /> --}}

@props([
    'href' => '#',
    'icon' => 'fa-solid fa-cube',
    'title' => '',
    'size' => 'sm', // sm = 1/3 width, full = full width
    'gradient' => 'from-blue-600 to-blue-800' // TailwindCSS gradient
])

@php
    $sizeClass = $size === 'full' ? 'w-full' : 'w-full sm:w-1/2 lg:w-1/3';
@endphp

<div class="{{ $sizeClass }} p-2">
    <a href="{{ $href }}" class="block group">
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br {{ $gradient }} p-6 min-h-[150px] flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.02]">
            {{-- Decorative background pattern --}}
            <div class="absolute inset-0 bg-white/5 group-hover:bg-white/10 transition-colors duration-300"></div>
            
            {{-- Content --}}
            <div class="relative text-center">
                <div class="mb-3">
                    <i class="{{ $icon }} text-white text-4xl group-hover:scale-110 transition-transform duration-300"></i>
                </div>
                <div class="text-white font-medium text-sm sm:text-base">{{ $title }}</div>
            </div>
        </div>
    </a>
</div>

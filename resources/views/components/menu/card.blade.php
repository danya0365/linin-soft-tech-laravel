@props([
    'href',
    'icon' => 'fa-circle',
    'title',
    'subtitle' => '',
    'gradient' => 'from-blue-500 to-blue-700',
    'subtitleColor' => 'text-blue-100'
])

<a href="{{ $href }}" class="group">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $gradient }} p-4 sm:p-6 h-44 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
        
        @if(isset($badge))
        <div class="absolute top-4 right-4">
            {{ $badge }}
        </div>
        @endif
        
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                <i class="{{ $icon }} text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold">{{ $title }}</h3>
            @if($subtitle)
            <p class="text-sm {{ $subtitleColor }}">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
</a>

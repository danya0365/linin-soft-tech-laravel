@props(['photo', 'alt' => 'Employee Avatar', 'class' => 'w-full h-full object-cover'])

<img 
    x-data="{ loaded: false }"
    x-init="$el.complete && $el.naturalWidth > 0 ? loaded = true : null"
    src="{{ $photo ? asset($photo) : asset('assets/avatar_placeholder.png') }}" 
    alt="{{ $alt }}"
    class="{{ $class }} transition-opacity duration-300"
    :class="loaded ? 'opacity-100' : 'opacity-0'"
    @load="loaded = true"
    onerror="this.onerror=null; this.src='{{ asset('assets/avatar_placeholder.png') }}'; this.dispatchEvent(new CustomEvent('load'))"
/>
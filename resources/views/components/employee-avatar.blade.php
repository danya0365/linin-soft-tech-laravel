@props(['photo', 'alt' => 'Employee Avatar', 'class' => 'w-full h-full object-cover'])

<img 
    src="{{ $photo ? asset($photo) : asset('assets/avatar_placeholder.png') }}" 
    alt="{{ $alt }}"
    class="{{ $class }}"
    onerror="this.onerror=null; this.src='{{ asset('assets/avatar_placeholder.png') }}';"
/>
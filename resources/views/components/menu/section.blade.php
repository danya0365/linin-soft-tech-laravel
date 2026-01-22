@props([
    'title',
    'icon' => 'fa-folder',
    'iconColor' => 'text-blue-500'
])

<div class="mb-8">
    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center">
        <i class="fa {{ $icon }} mr-2 {{ $iconColor }}"></i> {{ $title }}
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        {{ $slot }}
    </div>
</div>

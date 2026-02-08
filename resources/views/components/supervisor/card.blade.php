{{-- Supervisor Card Component (TailwindCSS) --}}
{{-- Usage: <x-supervisor.card title="Title"> content </x-supervisor.card> --}}

@props([
    'title' => '',
    'class' => ''
])

<div class="w-full">
    <div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden ' . $class]) }}>
        @if($title || isset($actions))
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                @if(isset($actions))
                <div class="flex gap-2">
                    {{ $actions }}
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="p-4 sm:p-6">
            {{ $slot }}
        </div>

        @if(isset($footer))
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
            {{ $footer }}
        </div>
        @endif
    </div>
</div>

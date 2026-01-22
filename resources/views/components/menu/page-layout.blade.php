@props([
    'title',
    'subtitle' => '',
    'icon' => 'fa-th-large',
    'iconColor' => 'text-blue-600',
    'breadcrumbs' => []
])

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-0 sm:px-4 py-8 max-w-7xl">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                        <i class="fa fa-home mr-2"></i>
                        Home
                    </a>
                </li>
                @foreach($breadcrumbs as $crumb)
                <li>
                    <div class="flex items-center">
                        <i class="fa fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                        @if(isset($crumb['url']))
                            <a href="{{ $crumb['url'] }}" class="text-sm font-medium text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                                {{ $crumb['label'] }}
                            </a>
                        @else
                            <span class="text-sm font-semibold text-gray-700 dark:text-white">{{ $crumb['label'] }}</span>
                        @endif
                    </div>
                </li>
                @endforeach
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fa {{ $icon }} mr-3 {{ $iconColor }}"></i>{{ $title }}
            </h1>
            @if($subtitle)
            <p class="text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
            @endif
        </div>

        <!-- Content -->
        {{ $slot }}
    </div>
</div>

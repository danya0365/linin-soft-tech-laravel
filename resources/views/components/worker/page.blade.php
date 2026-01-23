{{-- Worker Page Layout Component (TailwindCSS) --}}
{{-- Redesigned to match x-menu.page-layout with worker-specific identity --}}
{{-- Usage: <x-worker.page title="Title" :breadcrumbs="[...]"> content </x-worker.page> --}}

@props([
    'title' => '',
    'subtitle' => '',
    'icon' => 'fa-hard-hat',
    'iconColor' => 'text-amber-500',
    'breadcrumbs' => [] // Array of ['label' => '...', 'route' => '...'] or ['label' => '...'] for active item
])

<div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-gray-900 dark:to-gray-800">
    <div class="container">
        {{-- Breadcrumb Navigation --}}
        @if(count($breadcrumbs) > 0)
        <nav class="flex mb-6 sm:mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('worker') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-amber-600 dark:text-gray-400 dark:hover:text-amber-400 transition-colors">
                        <i class="fa fa-hard-hat mr-2 text-amber-500"></i>
                        Worker
                    </a>
                </li>
                @foreach($breadcrumbs as $crumb)
                <li>
                    <div class="flex items-center">
                        <i class="fa fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                        @if(isset($crumb['route']))
                            <a href="{{ $crumb['route'] }}" class="text-sm font-medium text-gray-500 hover:text-amber-600 dark:text-gray-400 dark:hover:text-amber-400 transition-colors">
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
        @endif

        {{-- Page Header with Worker Identity --}}
        @if($title)
        <div class="mb-6 sm:mb-8">
            <div class="flex items-center gap-3">
                {{-- Worker Badge --}}
                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fa {{ $icon }} text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $title }}
                    </h1>
                    @if($subtitle)
                    <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Page Content --}}
        {{ $slot }}
    </div>
</div>

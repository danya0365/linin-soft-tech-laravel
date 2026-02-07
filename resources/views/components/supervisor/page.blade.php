{{-- Supervisor Page Layout Component (TailwindCSS) --}}
{{-- Usage: <x-supervisor.page title="Title" :breadcrumbs="[...]"> content </x-supervisor.page> --}}

@props([
    'title' => '',
    'subtitle' => '',
    'icon' => 'fa-user-tie',
    'iconColor' => 'text-indigo-500',
    'breadcrumbs' => [] // Array of ['label' => '...', 'route' => '...'] or ['label' => '...'] for active item
])

<div class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="container">
        {{-- Breadcrumb Navigation --}}
        @if(count($breadcrumbs) > 0)
        <nav class="flex mb-6 sm:mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center">
                <li class="inline-flex items-center">
                    <a href="{{ route('supervisor') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors">
                        <i class="fa fa-user-tie mr-2 text-indigo-500"></i>
                        Supervisor
                    </a>
                </li>
                @foreach($breadcrumbs as $crumb)
                <li>
                    <div class="flex items-center">
                        <i class="fa fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                        @if(isset($crumb['route']))
                            <a href="{{ $crumb['route'] }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors">
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

        {{-- Page Header --}}
        @if($title)
        <div class="mb-6 sm:mb-8">
            <div class="flex items-center gap-3">
                {{-- Supervisor Badge --}}
                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-indigo-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
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

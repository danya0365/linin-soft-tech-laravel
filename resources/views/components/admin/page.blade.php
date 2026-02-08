@props([
    'title' => '',
    'subtitle' => '',
    'icon' => 'fa-cog',
    'iconColor' => 'text-slate-600',
    'breadcrumbs' => [] 
])

    <div class="container">
        {{-- Breadcrumb Navigation --}}
        @if(count($breadcrumbs) > 0)
        <nav class="flex mb-6 sm:mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-slate-600 dark:text-gray-400 dark:hover:text-slate-400 transition-colors">
                        <i class="fa fa-cogs mr-2 text-slate-500"></i>
                        Admin
                    </a>
                </li>
                @foreach($breadcrumbs as $crumb)
                <li>
                    <div class="flex items-center">
                        <i class="fa fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                        @if(isset($crumb['route']))
                            <a href="{{ $crumb['route'] }}" class="text-sm font-medium text-gray-500 hover:text-slate-600 dark:text-gray-400 dark:hover:text-slate-400 transition-colors">
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
                {{-- Admin Badge --}}
                <div class="flex-shrink-0 w-12 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center shadow-sm border border-slate-200 dark:border-gray-700">
                    <i class="fa {{ $icon }} {{ $iconColor }} text-xl"></i>
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


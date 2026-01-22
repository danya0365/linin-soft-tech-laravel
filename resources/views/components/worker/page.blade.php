{{-- Worker Page Container Component (TailwindCSS) --}}
{{-- Usage: <x-worker.page title="Title" :breadcrumbs="[...]"> content </x-worker.page> --}}

@props([
    'title' => '',
    'breadcrumbs' => [] // Array of ['label' => '...', 'route' => '...'] or ['label' => '...'] for active item
])

<div class="container">
    {{-- Breadcrumb Navigation --}}
    @if(count($breadcrumbs) > 0)
    <nav class="flex py-3" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('worker') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <i class="fa-solid fa-home w-4 h-4 mr-2"></i>
                    Worker
                </a>
            </li>
            @foreach($breadcrumbs as $crumb)
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right w-3 h-3 text-gray-400 mx-1"></i>
                    @if(isset($crumb['route']))
                        <a href="{{ $crumb['route'] }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">{{ $crumb['label'] }}</a>
                    @else
                        <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400">{{ $crumb['label'] }}</span>
                    @endif
                </div>
            </li>
            @endforeach
        </ol>
    </nav>
    @endif

    {{-- Page Content --}}
    <div class="flex flex-wrap justify-center">
        {{ $slot }}
    </div>
</div>

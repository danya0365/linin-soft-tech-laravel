{{--
    Breadcrumb Component (Redesigned to match worker/page.blade.php pattern)
    
    Usage:
    <x-crud.breadcrumb :items="[
        ['label' => 'Admin', 'route' => route('admin')],
        ['label' => 'Dryer Machines', 'route' => route('dryer-machines.index')],
        ['label' => 'Machine #1', 'route' => route('dryer-machines.show', $id)],
        ['label' => 'Create']
    ]" />
    
    Props:
    - items: Array of breadcrumb items
      Each item: ['label' => 'Text', 'route' => route('...')] or ['label' => 'Text'] for active item
      Note: 'route' should be a pre-resolved URL using route() helper, NOT a route name string
--}}

@props(['items' => []])

@if(count($items) > 0)
<nav class="flex mb-6 sm:mb-8" aria-label="Breadcrumb">
    <ol class="inline-flex items-center">
        @foreach($items as $index => $item)
        <li class="inline-flex items-center">
            @if($index > 0)
                <i class="fa fa-chevron-right text-gray-400 mx-2 text-xs"></i>
            @endif

            @if(isset($item['route']))
                <a href="{{ $item['route'] }}" 
                   class="text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors">
                    {{ $item['label'] }}
                </a>
            @else
                <span class="text-sm font-semibold text-gray-700 dark:text-white">{{ $item['label'] }}</span>
            @endif
        </li>
        @endforeach
    </ol>
</nav>
@endif

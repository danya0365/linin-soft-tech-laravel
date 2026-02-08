{{--
    Breadcrumb Component
    
    Usage:
    <x-crud.breadcrumb :items="[
        ['label' => 'Admin', 'route' => 'admin'],
        ['label' => 'Dryer Machines', 'route' => 'dryer-machines.index'],
        ['label' => 'Create']
    ]" />
    
    Props:
    - items: Array of breadcrumb items
      Each item: ['label' => 'Text', 'route' => 'route.name'] or ['label' => 'Text'] for active item
--}}

@props(['items' => []])

<nav class="mb-6" aria-label="Breadcrumb">
    <ol class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        @foreach($items as $index => $item)
            <li class="flex items-center gap-2">
                @if($index > 0)
                    <span class="text-gray-400 dark:text-gray-600">/</span>
                @endif
                
                @if(isset($item['route']))
                    <a href="{{ route($item['route']) }}" 
                       class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-gray-900 dark:text-white font-medium">
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>

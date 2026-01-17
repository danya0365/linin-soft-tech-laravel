{{--
    Page Header Component
    
    Usage:
    <x-crud.page-header 
        title="Dryer Machines"
        :createRoute="route('dryer-machines.create')"
        createLabel="Create New"
    />
    
    Or with custom actions:
    <x-crud.page-header title="Dryer Machine Detail">
        <x-slot:actions>
            <x-ui.button href="#">Add Note</x-ui.button>
            <x-ui.button variant="secondary" href="#">Back</x-ui.button>
        </x-slot:actions>
    </x-crud.page-header>
    
    Props:
    - title: Page title text
    - createRoute: Optional route for "Create New" button
    - createLabel: Optional label for create button (default: "Create New")
--}}

@props([
    'title',
    'createRoute' => null,
    'createLabel' => 'Create New'
])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
        {{ $title }}
    </h1>
    
    <div class="flex items-center gap-2">
        @isset($actions)
            {{ $actions }}
        @else
            @if($createRoute)
                <x-ui.button 
                    :href="$createRoute" 
                    variant="primary"
                    icon="fa fa-plus"
                    size="sm"
                >
                    {{ $createLabel }}
                </x-ui.button>
            @endif
        @endisset
    </div>
</div>

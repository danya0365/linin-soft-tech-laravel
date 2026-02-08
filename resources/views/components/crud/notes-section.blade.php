{{--
    Notes Section Component
    
    Usage:
    <x-crud.notes-section 
        :notes="$notes"
        :tags="$machineTags"
        :selectedTag="$selectedTag"
        :model="$dryerMachine"
        resource="dryer-machines"
    />
    
    Props:
    - notes: Paginated collection of notes
    - tags: Array of available tags
    - selectedTag: Currently selected tag filter (optional)
    - model: The parent model (machine/truck)
    - resource: Resource name for routing
--}}

@props([
    'notes',
    'tags' => [],
    'selectedTag' => null,
    'model',
    'resource'
])

<x-ui.card>
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notes</h3>
        </div>
    </x-slot:header>
    
    {{-- Tag Filter Section --}}
    @if(count($tags) > 0)
        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">
                    <i class="fa fa-tags"></i> Filter by Tag:
                </span>
                
                <x-ui.button 
                    :href="route($resource . '.show', $model->id)" 
                    size="sm"
                    :variant="!$selectedTag ? 'primary' : 'secondary'"
                >
                    All
                </x-ui.button>
                
                @foreach($tags as $tag)
                    <x-ui.button 
                        :href="route($resource . '.show', [$model->id, 'tag' => $tag])" 
                        size="sm"
                        :variant="$selectedTag == $tag ? 'primary' : 'outline'"
                    >
                        #{{ $tag }}
                    </x-ui.button>
                @endforeach
            </div>
            
            @if($selectedTag)
                <div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                    Showing: <strong>#{{ $selectedTag }}</strong>
                    <a href="{{ route($resource . '.show', $model->id) }}" 
                       class="ml-2 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                        <i class="fa fa-times"></i> Clear filter
                    </a>
                </div>
            @endif
        </div>
    @endif
    
    {{-- Notes List --}}
    <div class="space-y-4">
        @forelse ($notes as $note)
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                <div class="flex justify-content-between items-start mb-3">
                    <div class="flex-1">
                        <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                            Cost: {{ number_format($note->cost, 2) }} ฿
                        </h5>
                        
                        @if($note->tags && count($note->tags) > 0)
                            <div class="flex flex-wrap gap-1 mb-2">
                                @foreach($note->tags as $tag)
                                    <a href="{{ route($resource . '.show', [$model->id, 'tag' => $tag]) }}" 
                                       class="inline-block">
                                        <x-ui.badge 
                                            variant="primary"
                                            class="cursor-pointer hover:scale-110 transition-transform"
                                        >
                                            #{{ $tag }}
                                        </x-ui.badge>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <form action="{{ route('notes.destroy', $note->id) }}" method="POST" class="ml-4">
                        @csrf
                        @method('DELETE')
                        <x-ui.button 
                            type="submit"
                            variant="danger"
                            size="sm"
                            icon="fa fa-trash"
                            onclick="return confirm('Are you sure you want to delete this note?')"
                        >
                            Delete
                        </x-ui.button>
                    </form>
                </div>
                
                <p class="text-gray-700 dark:text-gray-300 mb-3">{{ $note->message }}</p>
                
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <i class="fa fa-calendar"></i> 
                    {{ $note->created_at->format('Y-m-d H:i') }}
                </p>
                
                @if($note->image_url && $note->image_url != '')
                    <div class="mt-3">
                        <img src="{{ asset($note->image_url) }}" 
                             alt="Note image" 
                             class="max-w-full h-auto rounded-lg shadow-md"
                             onerror="this.style.display='none'">
                    </div>
                @endif
            </div>
        @empty
            <x-ui.alert variant="info">
                @if($selectedTag)
                    No notes found with tag "#{{ $selectedTag }}"
                @else
                    No notes available yet
                @endif
            </x-ui.alert>
        @endforelse
    </div>
    
    {{-- Pagination --}}
    @if($notes->hasPages())
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            {{ $notes->appends(['tag' => $selectedTag])->links() }}
        </div>
    @endif
</x-ui.card>

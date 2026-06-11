{{--
    Action Buttons Component
    
    Usage:
    <x-crud.action-buttons 
        :model="$dryerMachine"
        resource="dryer-machines"
    />
    
    Props:
    - model: The model instance
    - resource: Resource name for routing (e.g., 'dryer-machines')
    - showView: Show "View" button (default: true)
    - showEdit: Show "Edit" button (default: true)
    - showDelete: Show "Delete" button (default: true)
--}}

@props([
    'model',
    'resource',
    'showView' => true,
    'showEdit' => true,
    'showDelete' => true
])

<div class="flex items-center gap-2">
    @if($showView)
        <x-ui.button 
            :href="route($resource . '.show', $model->id)"
            variant="primary"
            size="sm"
            icon="fa fa-eye"
        >
            Show
        </x-ui.button>
    @endif
    
    @if($showEdit)
        <x-ui.button 
            :href="route($resource . '.edit', $model->id)"
            variant="success"
            size="sm"
            icon="fa fa-edit"
        >
            Edit
        </x-ui.button>
    @endif
    
    @if($showDelete)
        <form action="{{ route($resource . '.destroy', $model->id) }}" method="POST" class="inline-block">
            @csrf
            @method('DELETE')
            <x-ui.button 
                type="submit"
                variant="danger"
                size="sm"
                icon="fa fa-trash"
                onclick="return confirm('Are you sure you want to delete this item?')"
            >
                Delete
            </x-ui.button>
        </form>
    @endif
</div>

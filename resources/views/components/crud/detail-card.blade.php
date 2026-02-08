{{--
    Detail Card Component
    
    Usage:
    <x-crud.detail-card 
        :model="$dryerMachine" 
        :fields="[
            'name' => 'Name',
            'photo' => 'Photo',
            'maximum_weight' => 'Maximum Weight'
        ]"
    />
    
    Props:
    - model: The model instance to display
    - fields: Associative array of 'field_name' => 'Label'
--}}

@props([
    'model',
    'fields' => []
])

<div class="grid md:grid-cols-2 gap-4">
    @foreach($fields as $field => $label)
        <div class="space-y-1">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $label }}
            </p>
            <p class="text-base font-semibold text-gray-900 dark:text-white">
                @if(str_contains($field, 'photo') || str_contains($field, 'image'))
                    @if($model->$field)
                        <img src="{{ asset($model->$field) }}" 
                             alt="{{ $label }}" 
                             class="max-w-xs rounded-lg shadow-md mt-2"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\' viewBox=\'0 0 200 200\'%3E%3Crect fill=\'%23e5e7eb\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23666\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                    @else
                        <span class="text-gray-400 dark:text-gray-600">No image available</span>
                    @endif
                @else
                    {{ $model->$field ?? '-' }}
                @endif
            </p>
        </div>
    @endforeach
</div>

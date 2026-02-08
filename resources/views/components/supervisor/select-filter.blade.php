{{-- Supervisor Select Filter Component (TailwindCSS) --}}
{{-- Usage: <x-supervisor.select-filter name="..." label="..." :options="[...]" :selected="..." /> --}}

@props([
    'name' => '',
    'label' => '',
    'options' => [], // Array of ['value' => ..., 'label' => ...] or simple ['key' => 'label'], or Collection with id/name
    'selected' => '',
    'showAll' => true,
    'showAllLabel' => 'แสดงทั้งหมด - Show All',
    'autoSubmit' => true
])

<div class="flex items-center gap-0 w-full">
    <label for="{{ $name }}" class="px-4 py-2.5 bg-gray-50 dark:bg-gray-600/50 text-gray-500 dark:text-gray-400 text-sm font-medium rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 whitespace-nowrap transition-colors duration-200">
        {{ $label }}
    </label>
    <select 
        id="{{ $name }}" 
        name="{{ $name }}" 
        class="px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-r-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full min-w-0 transition-all duration-200"
        @if($autoSubmit) onchange="this.form.submit()" @endif
    >
        @if($showAll)
            <option value="">{{ $showAllLabel }}</option>
        @endif
        @foreach($options as $key => $option)
            @if(is_object($option))
                 {{-- Handle Collection/Object with id/name --}}
                 <option value="{{ $option->id ?? $option->var }}" {{ $selected == ($option->id ?? $option->var) ? 'selected' : '' }}>{{ $option->name }}</option>
            @elseif(is_array($option) && isset($option['value']))
                <option value="{{ $option['value'] }}" {{ $selected == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
            @elseif(is_array($option) && isset($option['id'])) 
                {{-- Handle Array with id/name --}}
                <option value="{{ $option['id'] }}" {{ $selected == $option['id'] ? 'selected' : '' }}>{{ $option['name'] }}</option>
             @elseif(is_array($option) && isset($option['var'])) 
                {{-- Handle Array with var/name (for sort orders) --}}
                <option value="{{ $option['var'] }}" {{ $selected == $option['var'] ? 'selected' : '' }}>{{ $option['name'] }}</option>
            @else
                <option value="{{ $key }}" {{ $selected == $key ? 'selected' : '' }}>{{ $option }}</option>
            @endif
        @endforeach
    </select>
</div>

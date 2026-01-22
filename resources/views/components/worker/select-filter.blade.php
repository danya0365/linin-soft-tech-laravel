{{-- Worker Select Filter Component (TailwindCSS) --}}
{{-- Usage: <x-worker.select-filter name="..." label="..." :options="[...]" :selected="..." /> --}}

@props([
    'name' => '',
    'label' => '',
    'options' => [], // Array of ['value' => ..., 'label' => ...] or simple ['key' => 'label']
    'selected' => '',
    'showAll' => true,
    'showAllLabel' => 'แสดงทั้งหมด - Show All',
    'autoSubmit' => true
])

<div class="flex items-center gap-0">
    <label for="{{ $name }}" class="px-3 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 whitespace-nowrap">
        {{ $label }}
    </label>
    <select 
        id="{{ $name }}" 
        name="{{ $name }}" 
        class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-r-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[150px]"
        @if($autoSubmit) onchange="this.form.submit()" @endif
    >
        @if($showAll)
            <option value="">{{ $showAllLabel }}</option>
        @endif
        @foreach($options as $key => $option)
            @if(is_array($option) && isset($option['value']))
                <option value="{{ $option['value'] }}" {{ $selected == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
            @else
                <option value="{{ $key }}" {{ $selected == $key ? 'selected' : '' }}>{{ $option }}</option>
            @endif
        @endforeach
    </select>
</div>

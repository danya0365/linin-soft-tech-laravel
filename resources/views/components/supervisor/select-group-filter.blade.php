{{-- Supervisor Select Group Filter Component (TailwindCSS) --}}
{{-- Usage: <x-supervisor.select-group-filter name="..." label="..." :options="[...]" :selected="..." /> --}}

@props([
    'name' => '',
    'label' => '',
    'options' => [], // Array of groups
    'selected' => '',
    'showAll' => true,
    'showAllLabel' => 'แสดงทั้งหมด - Show All',
    'autoSubmit' => true,
    'groupLabelKey' => 'name',
    'groupOptionsKey' => 'customers',
    'optionValueKey' => 'id',
    'optionLabelKey' => 'name',
])

@php
    // Convert options to array to handle both objects and arrays
    $options = json_decode(json_encode($options), true);
@endphp

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
        @foreach($options as $group)
            <optgroup label="{{ $group[$groupLabelKey] ?? '' }}">
                @foreach($group[$groupOptionsKey] ?? [] as $option)
                    <option value="{{ $option[$optionValueKey] }}" {{ (string)$selected === (string)$option[$optionValueKey] ? 'selected' : '' }}>
                        {{ $option[$optionLabelKey] }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
</div>

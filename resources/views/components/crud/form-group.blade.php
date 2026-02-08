{{--
    Form Group Component
    
    Usage:
    <x-crud.form-group 
        name="name"
        label="Name"
        :value="$model->name ?? old('name')"
        placeholder="Enter name"
        required
    />
    
    <x-crud.form-group 
        name="description"
        label="Description"
        type="textarea"
        :value="$model->description"
        rows="5"
    />
    
    Props:
    - name: Input name attribute
    - label: Label text
    - type: text|email|number|tel|url|password|textarea|select (default: text)
    - value: Input value
    - placeholder: Placeholder text
    - required: Boolean for required field
    - help: Help text below input
    - options: Array for select dropdown ['value' => 'label']
--}}

@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'help' => null,
    'options' => [],
    'rows' => 3,
    'prefix' => null,
    'suffix' => null
])


@php
    $inputClasses = 'w-full px-4 py-2.5 border rounded-lg transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed';
    
    $hasError = $errors->has($name);
    $borderClasses = $hasError 
        ? 'border-red-500 ring-2 ring-red-200 dark:ring-red-900/50' 
        : 'border-gray-300 dark:border-gray-600';
@endphp

<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    @if($type === 'textarea')
        <textarea 
            name="{{ $name }}" 
            id="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => $inputClasses . ' ' . $borderClasses . ' resize-y']) }}
        >{{ old($name, $value) }}</textarea>
    
    @elseif($type === 'select')
        <select 
            name="{{ $name }}" 
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => $inputClasses . ' ' . $borderClasses . ' appearance-none bg-no-repeat bg-right pr-10']) }}
            style="background-image: url('data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'%3e%3cpath stroke=\'%236b7280\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6 8l4 4 4-4\'/%3e%3c/svg%3e'); background-position: right 0.5rem center; background-size: 1.5em 1.5em;"
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" {{ old($name, $value) == $optValue ? 'selected' : '' }}>
                    {{ $optLabel }}
                </option>
            @endforeach
        </select>
    
    @elseif($type === 'radio')
        <div class="flex flex-wrap items-center gap-6">
            @foreach($options as $optValue => $optLabel)
                <label class="inline-flex items-center cursor-pointer group">
                    <input 
                        type="radio" 
                        name="{{ $name }}" 
                        value="{{ $optValue }}"
                        {{ old($name, $value) == $optValue ? 'checked' : '' }}
                        {{ $required ? 'required' : '' }}
                        class="form-radio w-5 h-5 text-indigo-600 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:bg-gray-700 transition-all duration-200"
                    >
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200">{{ $optLabel }}</span>
                </label>
            @endforeach
        </div>
    @else
        <div class="relative rounded-md shadow-sm">
            @if(isset($prefix))
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 z-10">
                    <span class="text-gray-500 sm:text-sm">{{ $prefix }}</span>
                </div>
            @endif

            <input 
                type="{{ $type }}" 
                name="{{ $name }}" 
                id="{{ $name }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $attributes->merge(['class' => $inputClasses . ' ' . $borderClasses . (isset($prefix) ? ' pl-10' : '') . (isset($suffix) ? ' pr-10' : '')]) }}
            >

            @if(isset($suffix))
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 z-10">
                    <span class="text-gray-500 sm:text-sm">{{ $suffix }}</span>
                </div>
            @endif
        </div>
    @endif
    
    @if($help)
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif
    
    @error($name)
        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
            <i class="fa fa-exclamation-circle"></i>
            <span>{{ $message }}</span>
        </p>
    @enderror
</div>

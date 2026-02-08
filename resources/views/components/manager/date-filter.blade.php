{{-- Manager Date Filter Component (TailwindCSS) --}}
{{-- Usage: <x-manager.date-filter action="..." start-name="..." end-name="..." ... /> --}}

@props([
    'action' => '',
    'method' => 'GET',
    'id' => null,
    'startName' => 'date_start_at',
    'endName' => 'date_end_at',
    'startValue' => '',
    'endValue' => '',
    'startId' => null,
    'endId' => null,
    'resetUrl' => '',
    'submitLabel' => 'Submit',
    'resetLabel' => 'Reset'
])

<form id="{{ $id }}" class="flex flex-wrap items-end gap-4 mb-4" action="{{ $action }}" method="{{ $method }}">
    @if($method !== 'GET')
        @csrf
    @endif
    
    {{-- Date Range --}}
    <div class="flex items-center gap-0">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                 <i class="fa fa-calendar text-gray-400"></i>
            </div>
            <input 
                type="date" 
                name="{{ $startName }}" 
                id="{{ $startId ?? $startName }}"
                value="{{ $startValue }}" 
                class="pl-10 pr-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 rounded-l-lg"
                placeholder="Start" 
                aria-label="Start Date"
            >
        </div>
        <span class="px-3 py-2 bg-gray-100 dark:bg-gray-600 border-y border-gray-300 dark:border-gray-600 text-gray-500 text-sm">ถึง</span>
        <div class="relative">
             <input 
                type="date" 
                name="{{ $endName }}" 
                id="{{ $endId ?? $endName }}"
                value="{{ $endValue }}" 
                class="pl-3 pr-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 rounded-r-lg"
                placeholder="End" 
                aria-label="End Date"
            >
        </div>
    </div>

    {{-- Additional filter slots --}}
    {{ $slot }}

    {{-- Buttons --}}
    <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition-colors duration-200 shadow-sm hover:shadow flex items-center">
             <i class="fa fa-search mr-2"></i> {{ $submitLabel }}
        </button>
        @if($resetUrl)
        <a href="{{ $resetUrl }}" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 font-medium rounded-lg text-sm transition-colors duration-200 flex items-center">
             <i class="fa fa-refresh mr-2"></i> {{ $resetLabel }}
        </a>
        @else
        <button type="reset" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 font-medium rounded-lg text-sm transition-colors duration-200 flex items-center">
             <i class="fa fa-refresh mr-2"></i> {{ $resetLabel }}
        </button>
        @endif
    </div>
</form>

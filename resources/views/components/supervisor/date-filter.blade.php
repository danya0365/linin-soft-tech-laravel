{{-- Supervisor Date Filter Component (TailwindCSS) --}}
{{-- Usage: <x-supervisor.date-filter :action="..." :date-start-at="..." :date-end-at="..." :reset-url="..."> additional filters </x-supervisor.date-filter> --}}

@props([
    'action' => '',
    'dateStartAt' => '',
    'dateEndAt' => '',
    'resetUrl' => '',
    'method' => 'GET'
])

<form class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6" action="{{ $action }}" method="{{ $method }}">
    @if($method !== 'GET')
        @csrf
    @endif
    
    {{-- Date Range --}}
    <div class="md:col-span-2 lg:col-span-1 w-full">
        <div class="flex items-center gap-0 w-full">
            <span class="px-3 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 whitespace-nowrap">
                <i class="fa fa-calendar-alt text-indigo-500 dark:text-indigo-400"></i>
            </span>
            <input 
                type="date" 
                name="date_start_at" 
                value="{{ $dateStartAt }}" 
                class="px-2 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 flex-1 min-w-0 z-10 relative"
                placeholder="Start" 
                aria-label="Start Date"
            >
            <span class="px-2 py-2 bg-gray-100 dark:bg-gray-600 border border-l-0 border-gray-300 dark:border-gray-600 text-gray-500 text-sm z-0 relative">ถึง</span>
            <input 
                type="date" 
                name="date_end_at" 
                value="{{ $dateEndAt }}" 
                class="px-2 py-2 bg-white dark:bg-gray-700 border border-left-0 border-gray-300 dark:border-gray-600 rounded-r-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 flex-1 min-w-0 z-10 relative"
                placeholder="End" 
                aria-label="End Date"
            >
        </div>
    </div>

    {{-- Additional filter slots --}}
    {{ $slot }}

    {{-- Submit & Reset buttons --}}
    <div class="md:col-span-2 lg:col-span-4 flex justify-end gap-2">
        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition-colors duration-200 shadow-sm hover:shadow flex items-center">
             <i class="fa fa-search mr-2"></i> ค้นหา
        </button>
        @if($resetUrl)
        <a href="{{ $resetUrl }}" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 font-medium rounded-lg text-sm transition-colors duration-200 flex items-center">
             <i class="fa fa-refresh mr-2"></i> รีเซ็ต
        </a>
        @endif
    </div>
</form>

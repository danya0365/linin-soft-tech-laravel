{{-- Worker Date Filter Component (TailwindCSS) --}}
{{-- Usage: <x-worker.date-filter :action="..." :date-start-at="..." :date-end-at="..." :reset-url="..."> additional filters </x-worker.date-filter> --}}

@props([
    'action' => '',
    'dateStartAt' => '',
    'dateEndAt' => '',
    'resetUrl' => '',
    'method' => 'GET'
])

<form class="flex flex-wrap items-center gap-3 mb-4" action="{{ $action }}" method="{{ $method }}">
    @if($method !== 'GET')
        @csrf
    @endif
    
    {{-- Date Range --}}
    <div class="flex items-center gap-2">
        <input 
            type="date" 
            name="date_start_at" 
            value="{{ $dateStartAt }}" 
            class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="วันที่เริ่ม" 
            aria-label="วันที่เริ่ม"
        >
        <span class="px-2 py-2 bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm rounded-lg border border-gray-300 dark:border-gray-600">ถึง</span>
        <input 
            type="date" 
            name="date_end_at" 
            value="{{ $dateEndAt }}" 
            class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="วันที่สิ้นสุด" 
            aria-label="วันที่สิ้นสุด"
        >
    </div>

    {{-- Additional filter slots --}}
    {{ $slot }}

    {{-- Submit & Reset buttons --}}
    <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition-colors duration-200 shadow-sm hover:shadow">
            Submit
        </button>
        @if($resetUrl)
        <a href="{{ $resetUrl }}" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 font-medium rounded-lg text-sm transition-colors duration-200">
            Reset
        </a>
        @endif
    </div>
</form>

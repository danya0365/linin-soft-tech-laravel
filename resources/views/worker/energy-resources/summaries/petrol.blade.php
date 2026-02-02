@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="สถิติการใช้น้ำมันรถ" 
    subtitle="Petrol Statistics" 
    icon="fa-solid fa-gas-pump"
    :breadcrumbs="[
        ['label' => 'Energy Resource', 'route' => route('worker.energy-resource')],
        ['label' => 'Logs', 'route' => route('worker.energy-resource.logs')],
        ['label' => 'Petrol Stats']
    ]"
>
    <!-- Filter -->
    <x-worker.card title="ตัวกรอง (Filter)" icon="fa-solid fa-filter">
        <form action="{{ request()->url() }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <!-- Date Range -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ช่วงเวลา</label>
                <div class="flex items-center space-x-2">
                    <input type="date" name="date_start_at" value="{{ $dateStartAt }}" 
                           class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <span class="text-gray-500">ถึง</span>
                    <input type="date" name="date_end_at" value="{{ $dateEndAt }}" 
                           class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors shadow-sm text-sm font-medium">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ request()->url() }}" class="flex-1 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm text-sm font-medium text-center">
                    Reset
                </a>
            </div>
        </form>
    </x-worker.card>

    <!-- Chart -->
    <x-worker.card title="ปริมาณการใช้น้ำมันรถล่าสุด" icon="fa-solid fa-chart-bar">
        <div id="energy-chart" style="min-width: 100%; height: 400px; margin: 0 auto"></div>
    </x-worker.card>

</x-worker.page>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') return;
    $(function(){
        var energyDaysSummary = @json(App\Managers\HighChartManager::getEnergyDaysSummary());
        $.energyDaysChart({ 'renderTo': 'energy-chart', 'data': energyDaysSummary});
    })
});
</script>
@endpush
@endsection
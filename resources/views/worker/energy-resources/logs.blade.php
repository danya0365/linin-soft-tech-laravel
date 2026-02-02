@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="ประวัติการใช้พลังงาน" 
    subtitle="Energy Resource History & Logs" 
    icon="fa-solid fa-history"
    :breadcrumbs="[
        ['label' => 'Energy Resource', 'route' => route('worker.energy-resource')],
        ['label' => 'History']
    ]"
>
    <!-- Filter Section -->
    <x-worker.card title="ตัวกรอง (Filter)" icon="fa-solid fa-filter">
        <form action="{{ request()->url() }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            
            <!-- Energy Resource Select -->
            <div>
                <label for="energy_resource" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">พลังงาน</label>
                <select id="energy_resource" name="energy_resource" onchange="this.form.submit()" 
                        class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <option value="">แสดงทั้งหมด - Show All</option>
                    @foreach ($energyResources as $key => $energyResource)
                        <option value="{{ $energyResource['id'] }}" {{ $energyResourceSelected == $energyResource['id'] ? 'selected' : '' }}>
                            {{ $energyResource['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

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

            <!-- Sort Date -->
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">เรียงโดย</label>
                <select id="sort_order" name="sort_order" onchange="this.form.submit()" 
                        class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    @foreach ($sortOrders as $sortOrder)
                        <option value="{{ $sortOrder['var'] }}" {{ $sortOrderSelected == $sortOrder['var'] ? 'selected' : '' }}>
                            {{ $sortOrder['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="md:col-span-4 lg:col-span-4 flex justify-end gap-2 mt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors shadow-sm text-sm font-medium">
                    <i class="fa-solid fa-search mr-1"></i> Search
                </button>
                <a href="{{ route('worker.energy-resource.logs') }}" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm text-sm font-medium">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                </a>
            </div>
        </form>
    </x-worker.card>

    @if ($message = Session::get('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-4" role="alert">
            <p>{{ $message }}</p>
        </div>
    @endif

    <!-- Lists Table -->
    <x-worker.card title="รายการบันทึก (Logs)" icon="fa-solid fa-list">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">วันที่</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">พลังงาน</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">จำนวน</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">หน่วย</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ค่าใช้จ่าย</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">พนักงาน</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($energyResourceLogs as $energyResourceLog)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-center">
                            {{ $energyResourceLog->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-center">
                            <span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-200">
                                {{ $energyResourceLog->energyResource->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right font-medium">
                            {{ $energyResourceLog->value }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-left">
                            {{ $energyResourceLog->unit }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-left">
                            {{ number_format($energyResourceLog->cost) }} <span class="text-xs text-gray-500">THB</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $energyResourceLog->employee->name ?? "-" }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <form class="delete-form inline-block" action="{{ route('worker.energy-resource.logs.delete', $energyResourceLog->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {!! $energyResourceLogs->withQueryString()->links() !!}
        </div>
    </x-worker.card>

    <!-- Summary Table -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-worker.card title="สรุปตามประเภท (Summary)" icon="fa-solid fa-chart-pie">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">พลังงาน</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">จำนวนรวม</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ค่าใช้จ่ายรวม</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($energyResourceSums as $energyResourceSum)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $energyResourceSum->energyResource->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-center">
                                {{ number_format($energyResourceSum->total_value) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-center">
                                {{ number_format($energyResourceSum->total_cost) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-worker.card>

        <!-- Chart Section -->
        <x-worker.card title="สถิติ (Statistics)" icon="fa-solid fa-chart-line">
            <div id="energy-chart" style="min-width: 100%; height: 400px; margin: 0 auto"></div>
        </x-worker.card>
    </div>
</x-worker.page>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') return;
    
    $(function(){
        $('.delete-form').on('submit', function(e){
            if (!confirm("Are you sure?")) {
                return false;
            }
            return true
        })
    });
});
</script>
@endpush
@endsection
@extends('layouts.worker')

@section('content')
<x-worker.page
    :breadcrumbs="[
        ['label' => 'พนักงาน', 'route' => route('worker.employee')],
        ['label' => $employee['name'], 'route' => route('worker.employee.select-employee')]
    ]"
    title="{{ $employee['name'] }}"
    subtitle="สรุปข้อมูลพนักงาน"
    icon="fa-user"
>
    <x-worker.card title="สรุปข้อมูลของพนักงาน">
        {{-- Date Filter --}}
        <x-worker.date-filter 
            :action="route('worker.employee.employee-summary', ['employeeId' => $employee['id']])"
            :date-start-at="$dateStartAt"
            :date-end-at="$dateEndAt"
            :reset-url="route('worker.employee.employee-summary', ['employeeId' => $employee['id']])"
        />

        {{-- Employee Profile --}}
        <div class="flex flex-col items-center py-6">
            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-xl mb-4">
                <x-employee-avatar :photo="$employee['photo']" />
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $employee['name'] }}</h2>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium mt-2">
                <i class="fa fa-id-badge mr-1"></i> {{ $employee['code'] }}
            </span>
        </div>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @if (isset($summaryReports['wash']))
                @foreach ($summaryReports['wash'] as $summaryReport)
                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                    <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">{{ $summaryReport['title'] }}</div>
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ number_format($summaryReport['value']) }} <span class="text-sm font-normal">kg.</span></div>
                </div>
                @endforeach
            @endif

            @if (isset($summaryReports['dry']))
                @foreach ($summaryReports['dry'] as $summaryReport)
                <div class="bg-orange-50 dark:bg-orange-900/30 rounded-xl p-4 border border-orange-200 dark:border-orange-800">
                    <div class="text-sm text-orange-600 dark:text-orange-400 font-medium">{{ $summaryReport['title'] }}</div>
                    <div class="text-2xl font-bold text-orange-700 dark:text-orange-300">{{ number_format($summaryReport['value']) }} <span class="text-sm font-normal">kg.</span></div>
                </div>
                @endforeach
            @endif

            @if (isset($summaryReports['iron']))
                @foreach ($summaryReports['iron'] as $summaryReport)
                <div class="bg-purple-50 dark:bg-purple-900/30 rounded-xl p-4 border border-purple-200 dark:border-purple-800">
                    <div class="text-sm text-purple-600 dark:text-purple-400 font-medium">{{ $summaryReport['title'] }}</div>
                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ number_format($summaryReport['value']) }} <span class="text-sm font-normal">piece</span></div>
                </div>
                @endforeach
            @endif

            @if (isset($summaryReports['packing']))
                @foreach ($summaryReports['packing'] as $summaryReport)
                <div class="bg-green-50 dark:bg-green-900/30 rounded-xl p-4 border border-green-200 dark:border-green-800">
                    <div class="text-sm text-green-600 dark:text-green-400 font-medium">{{ $summaryReport['title'] }}</div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-300">{{ number_format($summaryReport['value']) }} <span class="text-sm font-normal">piece</span></div>
                </div>
                @endforeach
            @endif

            @if (isset($summaryReports['collect']))
                @foreach ($summaryReports['collect'] as $summaryReport)
                <div class="bg-teal-50 dark:bg-teal-900/30 rounded-xl p-4 border border-teal-200 dark:border-teal-800">
                    <div class="text-sm text-teal-600 dark:text-teal-400 font-medium">{{ $summaryReport['title'] }}</div>
                    <div class="text-2xl font-bold text-teal-700 dark:text-teal-300">{{ number_format($summaryReport['value']) }} <span class="text-sm font-normal">kg.</span></div>
                </div>
                @endforeach
            @endif

            @if (isset($summaryReports['collect_pack']))
                @foreach ($summaryReports['collect_pack'] as $summaryReport)
                <div class="bg-cyan-50 dark:bg-cyan-900/30 rounded-xl p-4 border border-cyan-200 dark:border-cyan-800">
                    <div class="text-sm text-cyan-600 dark:text-cyan-400 font-medium">{{ $summaryReport['title'] }}</div>
                    <div class="text-2xl font-bold text-cyan-700 dark:text-cyan-300">{{ number_format($summaryReport['value']) }} <span class="text-sm font-normal">pack</span></div>
                </div>
                @endforeach
            @endif
        </div>

        {{-- Chart --}}
        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
            <div id="employee-summary-scatter-plots-chart" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
        </div>

        {{-- Working Duration Footer --}}
        <x-slot name="footer">
            <div class="text-center">
                <span class="text-gray-500 dark:text-gray-400">
                    <i class="fa fa-clock mr-2"></i>เวลาการทำงานทั้งหมด: 
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $workingDuration }}</span>
                </span>
            </div>
        </x-slot>
    </x-worker.card>
</x-worker.page>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') return;
    
    var weekDayReports = @json($weekDayReports);
    $(function(){
        $.employeeSummaryScatterPlotsChart({
            renderTo: "employee-summary-scatter-plots-chart",
            data: weekDayReports
        });
    });
});
</script>
@endpush
@endsection
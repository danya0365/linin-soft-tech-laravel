@extends('layouts.supervisor')

@section('content')
<x-supervisor.page 
    title="{{ __('รายงานสถิติ') }}"
    subtitle="Report" 
    icon="fa-chart-pie"
    :breadcrumbs="[
        ['label' => 'Supervisor', 'route' => route('supervisor')],
        ['label' => __('รายงานสถิติ')]
    ]"
>
    
    <div class="space-y-6">
        {{-- Latest Day Chart --}}
        <x-supervisor.card title="ต้นทุนแต่ละวัน">
            <div class="p-4">
                <div id="expense-latest-day-chart" class="w-full h-96"></div>
            </div>
        </x-supervisor.card>

        {{-- Range Days Chart --}}
        <x-supervisor.card title="ต้นทุนตามวันที่เลือก">
            <div class="p-4">
                <form id="expense-range-days-form" class="mb-6" action="{{ request()->url() }}" method="GET">
                    <div class="flex flex-col md:flex-row items-end gap-4">
                        <div class="w-full md:w-auto flex-grow max-w-lg">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ช่วงวันที่</label>
                            <div class="flex items-center gap-0 w-full">
                                <span class="px-3 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 whitespace-nowrap">
                                    <i class="fa fa-calendar-alt text-indigo-500"></i>
                                </span>
                                <input 
                                    type="date" 
                                    name="expense-range-days-start-at" 
                                    class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 flex-1 min-w-0 z-10 relative"
                                    placeholder="Start" 
                                >
                                <span class="px-3 py-2 bg-gray-100 dark:bg-gray-600 border border-l-0 border-r-0 border-gray-300 dark:border-gray-600 text-gray-500 text-sm z-0 relative">ถึง</span>
                                <input 
                                    type="date" 
                                    name="expense-range-days-end-at" 
                                    class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-r-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 flex-1 min-w-0 z-10 relative"
                                    placeholder="End" 
                                >
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 flex items-center">
                                <i class="fa fa-search mr-2"></i> Submit
                            </button>
                            <button type="reset" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 font-medium rounded-lg shadow-sm transition-colors duration-200">
                                Reset
                            </button>
                        </div>
                    </div>
                </form>

                <div id="expense-range-days-chart" class="w-full h-96"></div>
            </div>
        </x-supervisor.card>
    </div>

</x-supervisor.page>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') return;
    
    $(function(){

        var expenseDaysSummary = @json(App\Managers\HighChartManager::getExpenseDaysSummary());
        // Check if Highcharts is available before initializing
        if(typeof $.fn.expenseDaysChart === 'function') {
             $.expenseDaysChart({ 'renderTo': 'expense-latest-day-chart', 'data': expenseDaysSummary});
        } elseif(typeof Highcharts !== 'undefined') {
            // Fallback simplistic rendering if custom plugin missing (assumption)
             Highcharts.chart('expense-latest-day-chart', {
                title: { text: null },
                series: [{ data: expenseDaysSummary }]
             });
        }

        var expenseRangeDaysSummary = () => {
            var startAt = $('[name=expense-range-days-start-at]').val();
            var endAt = $('[name=expense-range-days-end-at]').val();

            if (!startAt || !endAt) {
                Swal.fire({
                    icon: 'warning',
                    text: 'กรุณาเลือกวันเริ่มต้นและสิ้นสุด'
                });
                return;
            }

            var url = '{!! route('api.expense-range-days-chart', ['startAt' => 'startAtParam', 'endAt' => 'endAtParam']) !!}';
            
            url = url.replace('startAtParam', startAt)
            url = url.replace('endAtParam', endAt)

            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                },
            });

            $.get(url, function(response){
                if(typeof $.fn.expenseDaysChart === 'function') {
                    $.expenseDaysChart({ 'renderTo': 'expense-range-days-chart', 'data': response, 'title': `ยอดต้นทุน วันที่ ${startAt} ถึง ${endAt}`});
                }
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    text: 'กรุณาลองใหม่อีกครั้ง'
                })
            }).always(function() {
                Swal.close();
            });
        }

        $('#expense-range-days-form').on('submit', (e) => {
            e.preventDefault();
            expenseRangeDaysSummary();
        })

        // Removed datepicker initialization as we use input type="date"
    })
});
</script>
@endpush
@endsection
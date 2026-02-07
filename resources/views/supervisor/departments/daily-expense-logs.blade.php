@extends('layouts.supervisor')

@section('content')
<x-supervisor.page 
    title="ประวัติค่าใช้จ่ายรายวัน" 
    subtitle="Daily Expense Logs" 
    icon="fa-history"
    :breadcrumbs="[
        ['label' => 'Supervisor', 'route' => route('supervisor')],
        ['label' => __('Department'), 'route' => route('supervisor.department')],
        ['label' => 'ประวัติค่าใช้จ่ายรายวัน']
    ]"
>
    {{-- Success Message --}}
    @if ($message = Session::get('success'))
    <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 flex items-center">
        <i class="fa fa-check-circle mr-2"></i>
        {{ $message }}
    </div>
    @endif

    {{-- Filters --}}
    <x-supervisor.date-filter 
        :action="request()->url()" 
        :date-start-at="$dateStartAt" 
        :date-end-at="$dateEndAt" 
        :reset-url="route('supervisor.department.daily-expense-log')"
    >
        <div class="md:col-span-1">
            <x-supervisor.select-filter 
                name="department_id" 
                label="แผนก" 
                :options="$departments" 
                :selected="$departmentSelected" 
            />
        </div>
        <div class="md:col-span-1">
             <x-supervisor.select-filter 
                name="sort_order" 
                label="เรียงโดย" 
                :options="$sortOrders" 
                :selected="$sortOrderSelected" 
                show-all="false"
            />
        </div>
    </x-supervisor.date-filter>

    {{-- Summary Table --}}
    <div class="mb-6">
        <x-supervisor.card title="สรุปยอดรวม (Summary)">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">แผนก</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">จำนวนเงิน (บาท)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($departmentDailyCostSums as $departmentDailyCostSum)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $departmentDailyCostSum->department->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-indigo-600 dark:text-indigo-400 font-bold">
                                {{ number_format($departmentDailyCostSum->total_cost, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-supervisor.card>
    </div>

    {{-- Logs Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @foreach ($departmentDailyCostLogs as $log)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300 flex flex-col h-full">
            {{-- Header --}}
            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600 flex justify-between items-start">
                <div>
                    <h5 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $log->department->name }}</h5>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->daily_date }}</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                    ฿{{ number_format($log->cost) }}
                </span>
            </div>

            {{-- Image --}}
            @if($log->image_url)
            <div class="h-48 w-full bg-gray-200 dark:bg-gray-900 relative group overflow-hidden">
                <img src="{{ asset($log->image_url) }}" alt="Receipt" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
            </div>
            @endif

            {{-- Body --}}
            <div class="p-5 flex-grow">
                @if($log->message)
                <p class="text-gray-600 dark:text-gray-300 text-sm italic">"{{ $log->message }}"</p>
                @else
                <p class="text-gray-400 dark:text-gray-500 text-sm italic">- ไม่มีบันทึกข้อความ -</p>
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-750 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <small class="text-xs text-gray-500 dark:text-gray-400">
                    บันทึกเมื่อ: {{ $log->created_at->format('Y-m-d') }}
                </small>
                
                <form class="delete-form" action="{{ route('supervisor.department.delete-daily-expense-log', ['id' => $log->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium transition-colors">
                        <i class="fa fa-trash"></i> ลบ
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $departmentDailyCostLogs->withQueryString()->links() }}
    </div>

</x-supervisor.page>

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
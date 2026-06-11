@extends('layouts.user-customer')

@section('content')

@extends('layouts.user-customer')

@section('content')

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumbs --}}
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('user-customer') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-200">
                    User Customer
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                    <a href="{{ route('user-customer.customer') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-200">ลูกค้า</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">รายการยอดรวม</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">รายการยอดรวมแต่ละลูกค้า</h3>
        </div>

        <div class="p-6">
            {{-- Filter Form --}}
            <form class="mb-8 bg-gray-50 dark:bg-gray-700/50 p-6 rounded-xl border border-gray-200 dark:border-gray-600" action="{{ route('user-customer.customer.operation-summary') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
                    
                    {{-- Date Range --}}
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ช่วงวันที่</label>
                        <div class="flex items-center gap-2">
                            <input type="date" name="date_start_at" value="{{ $dateStartAt }}" class="block w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white">
                            <span class="text-gray-500">ถึง</span>
                            <input type="date" name="date_end_at" value="{{ $dateEndAt }}" class="block w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white">
                        </div>
                    </div>

                    {{-- Sort Order --}}
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เรียงโดย</label>
                        <select class="block w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white" 
                                id="sort_order" name="sort_order" onchange="this.form.submit()">
                            @foreach ( $sortOrders as $sortOrder )
                            <option value="{{ $sortOrder['var'] }}" {{ $sortOrderSelected == $sortOrder['var'] ? 'selected' : '' }}>{{ $sortOrder['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 flex items-center gap-2">
                            <i class="fa fa-search"></i> ค้นหา
                        </button>
                        <a href="{{ route('user-customer.customer.operation-summary') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center gap-2">
                            <i class="fa fa-refresh"></i> รีเซ็ต
                        </a>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <div class="overflow-x-auto relative shadow-md sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">ลูกค้า</th>
                            <th scope="col" class="px-6 py-3 text-center">น้ำหนักผ้าเปียก</th>
                            <th scope="col" class="px-6 py-3 text-center">น้ำหนักผ้าสะอาด</th>
                            <th scope="col" class="px-6 py-3 text-center" title="คำนวณอัตโนมัติจาก linen_case='edit'">
                                ผ้าแก้ไข (ระบบ) <i class="fa fa-info-circle text-green-500 ml-1"></i>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center" title="กรอกโดย Supervisor ตอนออกบิล">
                                ผ้าแก้ไข (บันทึกมือ) <i class="fa fa-info-circle text-amber-500 ml-1"></i>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center">% ของเสีย</th>
                            <th scope="col" class="px-6 py-3 text-center">น้ำหนักลูกค้า</th>
                            <th scope="col" class="px-6 py-3 text-center">มากกว่าหรือน้อยกว่า</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($operations as $operation)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    @if ( $operation->customer )
                                    <a href="{{ route('user-customer.customer.get-operations-by-customer', ['customerId' => $operation->customer->id]) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 hover:underline">
                                        {{ $operation->customer->name }}
                                    </a>
                                    @else
                                    <span class="italic text-gray-400">ลูกค้าถูกลบ</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">{{ number_format($operation->total_wet_weight) }}</td>
                                <td class="px-6 py-4 text-center">{{ number_format($operation->total_collect_weight) }}</td>
                                <td class="px-6 py-4 text-center text-green-600 dark:text-green-400 font-medium">{{ number_format($operation->total_edit_collect_weight) }}</td>
                                <td class="px-6 py-4 text-center text-amber-600 dark:text-amber-400 font-medium">{{ number_format($operation->total_edit_weight ?? 0) }}</td>
                                <td class="px-6 py-4 text-center">{{ number_format($operation->total_collect_weight > 0 ? $operation->total_edit_collect_weight*100/$operation->total_collect_weight : 0) }}%</td>
                                <td class="px-6 py-4 text-center font-semibold text-gray-900 dark:text-white">{{ number_format($operation->total_billing_weight) }}</td>
                                <td class="px-6 py-4 text-center {{ ($operation->total_billing_weight-$operation->total_collect_weight) < 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    {{ number_format($operation->total_billing_weight-$operation->total_collect_weight) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white">
                        <tr>
                            <td class="px-6 py-3">ยอดรวม</td>
                            <td class="px-6 py-3 text-center">{{ number_format($summary->total_wet_weight) }}</td>
                            <td class="px-6 py-3 text-center">{{ number_format($summary->total_collect_weight) }}</td>
                            <td class="px-6 py-3 text-center text-green-600 dark:text-green-400">{{ number_format($summary->total_edit_collect_weight) }}</td>
                            <td class="px-6 py-3 text-center text-amber-600 dark:text-amber-400">{{ number_format($summary->total_edit_weight ?? 0) }}</td>
                            <td class="px-6 py-3 text-center">{{ number_format($summary->total_collect_weight > 0 ? $summary->total_edit_collect_weight*100/$summary->total_collect_weight : 0) }}%</td>
                            <td class="px-6 py-3 text-center">{{ number_format($summary->total_billing_weight) }}</td>
                            <td class="px-6 py-3 text-center {{ ($summary->total_billing_weight-$summary->total_collect_weight) < 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ number_format($summary->total_billing_weight-$summary->total_collect_weight) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="mt-6">
                {!! $operations->withQueryString()->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@endsection
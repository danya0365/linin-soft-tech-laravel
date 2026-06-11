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
                    <a href="{{ route('user-customer.customer.operation-summary') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-200">{{ $customer['name'] }}</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">รายการทั้งหมด</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                รายการทั้งหมดของ {{ $customer['name'] }}
            </h3>
        </div>

        <div class="p-6">
            {{-- Filter Form --}}
            <form class="mb-8 bg-gray-50 dark:bg-gray-700/50 p-6 rounded-xl border border-gray-200 dark:border-gray-600" action="{{ route('user-customer.customer.get-operations-by-customer', ['customerId' => $customer['id']]) }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 items-end">
                    
                    {{-- Date Range --}}
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ช่วงวันที่</label>
                        <div class="flex items-center gap-2">
                            <input type="date" name="date_start_at" value="{{ $dateStartAt }}" class="block w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white">
                            <span class="text-gray-500">ถึง</span>
                            <input type="date" name="date_end_at" value="{{ $dateEndAt }}" class="block w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white">
                        </div>
                    </div>

                    {{-- Operation Type --}}
                    <div>
                        <label for="operation_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ประเภทงาน</label>
                        <select class="block w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white" 
                                id="operation_type" name="operation_type" onchange="this.form.submit()">
                            <option value="">แสดงทั้งหมด - Show All</option>
                            @foreach ( $operationTypes as $key => $operationType )
                            <option value="{{ $key }}" {{ $operationTypeSelected == $key ? 'selected' : '' }}>{{ $operationType }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Linen Product --}}
                    <div>
                        <label for="linen_product_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สินค้า</label>
                        <select class="block w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white" 
                                id="linen_product_id" name="linen_product_id" onchange="this.form.submit()">
                            <option value="">แสดงทั้งหมด - Show All</option>
                            @foreach ( $linenTypes as $linenType )
                            <optgroup label="{{ $linenType['name'] }}">
                                @foreach ( $linenType['linen_products'] as $linenProduct )
                                <option value="{{ $linenProduct['id'] }}" {{ $linenProductSelected == $linenProduct['id'] ? 'selected' : '' }}>{{ $linenProduct['name'] }}</option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>

                    {{-- Linen Case --}}
                    <div>
                        <label for="linen_case" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ชนิด</label>
                        <select class="block w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:text-white" 
                                id="linen_case" name="linen_case" onchange="this.form.submit()">
                            <option value="">แสดงทั้งหมด - Show All</option>
                            @foreach ( $linenCases as $key => $linenCase )
                            <option value="{{ $linenCase['var'] }}" {{ $linenCaseSelected == $linenCase['var'] ? 'selected' : '' }}>{{ $linenCase['name'] }}</option>
                            @endforeach
                        </select>
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
                    <div class="col-span-1 lg:col-span-2 xl:col-span-2 flex gap-2 justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 flex items-center gap-2">
                            <i class="fa fa-search"></i> ค้นหา
                        </button>
                        <a href="{{ route('user-customer.customer.get-operations-by-customer', ['customerId' => $customer['id']]) }}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center gap-2">
                            <i class="fa fa-refresh"></i> รีเซ็ต
                        </a>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <div class="overflow-x-auto relative shadow-md sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">วันที่</th>
                            <th scope="col" class="px-4 py-3 text-left">ประเภทงาน</th>
                            <th scope="col" class="px-4 py-3 text-left">สินค้า</th>
                            <th scope="col" class="px-4 py-3 text-left">ชนิด</th>
                            <th scope="col" class="px-4 py-3">สี</th>
                            <th scope="col" class="px-4 py-3 text-left">พนักงาน</th>
                            <th scope="col" class="px-4 py-3">จำนวนที่ซัก (kg.)</th>
                            <th scope="col" class="px-4 py-3">จำนวนที่อบ (kg.)</th>
                            <th scope="col" class="px-4 py-3">จำนวนที่รีด (piece)</th>
                            <th scope="col" class="px-4 py-3">จำนวนที่พับแพ็ค (piece)</th>
                            <th scope="col" class="px-4 py-3">น้ำหนักที่จัดเก็บ (kg.)</th>
                            <th scope="col" class="px-4 py-3">จำนวนที่จัดเก็บ (pack)</th>
                            <th scope="col" class="px-4 py-3">จำนวนที่ขนส่ง (pack)</th>
                            <th scope="col" class="px-4 py-3">เวลา</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($operations as $operation)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150">
                                <td class="px-4 py-3 text-left whitespace-nowrap">{{ $operation->created_at->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-left">{{ App\Enums\OperationType::getDescription($operation->operation->operation_type) }}</td>
                                <td class="px-4 py-3 text-left font-medium text-gray-900 dark:text-white">{{ $operation->linenProduct ? $operation->linenProduct->name : '-' }}</td>
                                <td class="px-4 py-3 text-left">{{ $operation->linen_case }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-1 rounded text-xs text-white shadow-sm" style="background-color: {{ $operation->color }}">
                                        {{ $operation->color }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-left">{{ $operation->operation->employee->name ?? "" }}</td>
                                <td class="px-4 py-3">
                                    {{ $operation->wet_weight }}
                                    <span class="text-xs text-gray-400 block">(#{{ $operation->operation->washing_machine_id }})</span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $operation->dry_weight }}
                                    <span class="text-xs text-gray-400 block">(#{{ $operation->operation->dryer_machine_id }})</span>
                                </td>
                                <td class="px-4 py-3">{{ $operation->iron_piece }}</td>
                                <td class="px-4 py-3">{{ $operation->packing_piece }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $operation->collect_weight }}</td>
                                <td class="px-4 py-3">{{ $operation->collect_pack }}</td>
                                <td class="px-4 py-3">
                                    {{ $operation->deliver_pack }}
                                    <span class="text-xs text-gray-400 block">(#{{ $operation->operation->truck_id }})</span>
                                </td>
                                <td class="px-4 py-3 text-gray-400">{{ $operation->created_at->format('H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
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
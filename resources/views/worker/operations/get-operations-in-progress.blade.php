@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="รายการทั้งหมดที่ยังไม่ถูกปิดงาน"
    subtitle="Operation In Progress"
    icon="fa-solid fa-hourglass-half"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'รายการทั้งหมด']
    ]"
>
    <x-worker.card>
        {{-- Filters --}}
        <div class="mb-6">
            <form action="{{ route('worker.operation.in-progress') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Linen Type --}}
                <div>
                    <label for="linen-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ชนิดผ้า</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa fa-tshirt text-gray-400"></i>
                        </div>
                        <select id="linen-type" name="linenType" onchange="this.form.submit()" 
                            class="pl-10 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                            <option value="">ไม่เลือก</option>
                            @foreach ( $linenTypes as $linenType )
                            <option value="{{ $linenType->id }}" {{ $linenTypeSelected == $linenType->id ? 'selected' : '' }}>{{ $linenType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Operation Type --}}
                <div>
                    <label for="operation_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ประเภทงาน</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa fa-cogs text-gray-400"></i>
                        </div>
                        <select id="operation_type" name="operation_type" onchange="this.form.submit()"
                            class="pl-10 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                            <option value="">แสดงทั้งหมด - Show All</option>
                            @foreach ( $operationTypes as $key => $operationType )
                            <option value="{{ $key }}" {{ $operationTypeSelected == $key ? 'selected' : '' }}>{{ $operationType }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Date Range --}}
                <div class="md:col-span-2 lg:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">วันที่</label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="date_start_at" value="{{ $dateStartAt }}" 
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                        <span class="text-gray-500">-</span>
                        <input type="date" name="date_end_at" value="{{ $dateEndAt }}" 
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                    </div>
                </div>

                {{-- Sort Order --}}
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">เรียงโดย</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa fa-sort text-gray-400"></i>
                        </div>
                        <select id="sort_order" name="sort_order" onchange="this.form.submit()"
                            class="pl-10 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                            @foreach ( $sortOrders as $sortOrder )
                            <option value="{{ $sortOrder['var'] }}" {{ $sortOrderSelected == $sortOrder['var'] ? 'selected' : '' }}>{{ $sortOrder['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="md:col-span-2 lg:col-span-4 flex justify-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        <i class="fa fa-search mr-1"></i> ค้นหา
                    </button>
                    <a href="{{ route('worker.operation.in-progress') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition-colors shadow-sm">
                        <i class="fa fa-refresh mr-1"></i> รีเซ็ต
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <x-worker.data-table 
            :headers="['วันที่', 'ประเภทงาน', 'สินค้า', 'สี', 'ลูกค้า', 'พนักงาน', 'ซัก (kg)', 'อบ (kg)', 'รีด (pc)', 'แพ็ค (pc)', 'เก็บ (kg)', 'เก็บ (pk)', 'ส่ง (pk)', 'เวลา']"
            :paginator="$operations"
        >
            @foreach ($operations as $operation)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 whitespace-nowrap">{{ $operation->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">
                        <a href="{{ route('worker.operation.checkout-in-progress', $operation->operation->id) }}" class="text-amber-600 hover:text-amber-700 font-medium">
                            {{ App\Enums\OperationType::getDescription($operation->operation->operation_type) }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $operation->linenProduct ? $operation->linenProduct->name : '-' }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full border border-gray-200 dark:border-gray-600 shadow-sm" style="background-color: {{ $operation->color }}"></span>
                            <span class="text-gray-600 dark:text-gray-400">{{ $operation->color }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $operation->operation->customer ? $operation->operation->customer->name : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $operation->operation->employee ? $operation->operation->employee->name : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-200">
                        <div>{{ $operation->wet_weight }}</div>
                        @if($operation->operation->washing_machine_id)
                        <div class="text-xs text-gray-500">#{{ $operation->operation->washing_machine_id }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-200">
                        <div>{{ $operation->dry_weight }}</div>
                        @if($operation->operation->dryer_machine_id)
                        <div class="text-xs text-gray-500">#{{ $operation->operation->dryer_machine_id }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-200">{{ $operation->iron_piece }}</td>
                    <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-200">{{ $operation->packing_piece }}</td>
                    <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-200">{{ $operation->collect_weight }}</td>
                    <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-200">{{ $operation->collect_pack }}</td>
                    <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-200">
                        <div>{{ $operation->deliver_pack }}</div>
                        @if($operation->operation->truck_id)
                        <div class="text-xs text-gray-500">#{{ $operation->operation->truck_id }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 whitespace-nowrap">{{ $operation->created_at->format('H:i') }}</td>
                </tr>
            @endforeach
        </x-worker.data-table>
    </x-worker.card>
</x-worker.page>
@endsection
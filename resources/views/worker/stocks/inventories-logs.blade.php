@extends('layouts.worker')

@section('content')

<x-worker.page :breadcrumbs="[
    ['label' => 'Stocks', 'route' => route('worker.stock')],
    ['label' => 'ประวัติสต๊อก - Stock history']
]">
    @if ($message = Session::get('success'))
    <div class="w-full px-2 mb-2">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ $message }}
        </div>
    </div>
    @endif

    @if ($message = Session::get('error'))
    <div class="w-full px-2 mb-2">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            {{ $message }}
        </div>
    </div>
    @endif

    <x-worker.card title="ประวัติสต๊อก - Stock history">
        <x-worker.date-filter 
            :action="request()->url()" 
            :date-start-at="$dateStartAt" 
            :date-end-at="$dateEndAt"
            :reset-url="route('worker.stock.inventories-log')"
        >
            <x-worker.select-filter 
                name="inventory_group_id"
                label="ประเภทสต๊อก"
                :options="$inventoryGroups->pluck('name', 'id')->toArray()"
                :selected="$inventoryGroupSelected"
            />
            
            <x-worker.select-filter 
                name="sort_order"
                label="เรียงโดย"
                :options="collect($sortOrders)->pluck('name', 'var')->toArray()"
                :selected="$sortOrderSelected"
                :show-all="false"
            />
        </x-worker.date-filter>

        <x-worker.data-table 
            :headers="['วันที่ - Date', 'Name', 'Import/Export', 'จำนวน - Count', 'ค่าใช้จ่าย - Cost (Thai Baht)', '']"
            :paginator="$inventoryLogs"
        >
            @foreach ($inventoryLogs as $inventoryLog)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $inventoryLog->created_at->format('Y-m-d') }}</td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">
                    @if ($inventoryLog->inventory && $inventoryLog->inventory?->inventoryGroup)
                        {{ $inventoryLog->inventory?->inventoryGroup?->name ?? '-' }}
                        : {{ $inventoryLog->inventory?->name ?? '-' }}
                    @else
                        -
                    @endif
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $inventoryLog->type === 'import' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $inventoryLog->type }}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $inventoryLog->quantity }}</td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ number_format($inventoryLog->cost) }}</td>
                <td class="px-4 py-3 text-sm">
                    <form class="delete-form" action="{{ route('worker.stock.inventory.logs.delete', $inventoryLog->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fa fa-fw fa-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </x-worker.data-table>
    </x-worker.card>
</x-worker.page>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!confirm("Are you sure?")) {
                e.preventDefault();
                return false;
            }
        });
    });
});
</script>
@endpush
@endsection
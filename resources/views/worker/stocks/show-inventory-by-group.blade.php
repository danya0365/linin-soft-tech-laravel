@extends('layouts.worker')

@section('content')
<x-worker.page
    :breadcrumbs="[
        ['label' => __('Stocks'), 'route' => route('worker.stock')]
    ]"
    title="{{ $inventoryGroup->name }}"
    subtitle="รายการทั้งหมด"
>
    {{-- Success/Error Messages --}}
    @if ($message = Session::get('success'))
    <div class="w-full mb-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ $message }}
        </div>
    </div>
    @endif

    @if ($message = Session::get('error'))
    <div class="w-full mb-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            {{ $message }}
        </div>
    </div>
    @endif

    <x-worker.card>
        <x-slot name="actions">
            <x-ui.button :href="route('worker.stock.create-inventory-by-group', ['inventoryGroupId' => $inventoryGroup->id])" variant="primary" icon="fa fa-plus">
                {{ __('Create New') }}
            </x-ui.button>
        </x-slot>

        <x-worker.data-table 
            :headers="['วันที่อัพเดต', 'สินค้า', 'สต๊อกทั้งหมด', 'สต๊อกคงเหลือ', 'Unit', 'Actions']"
            :paginator="$inventories"
        >
            @foreach ($inventories as $inventory)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">{{ $inventory->updated_at->format('Y-m-d') }}</td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 font-medium">{{ $inventory->name }}</td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $inventory->total_quantity }}</td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $inventory->remain_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $inventory->remain_quantity }}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-center">{{ $inventory->unit }}</td>
                <td class="px-4 py-3 text-sm">
                    <form class="delete-form flex items-center gap-1 justify-center" action="{{ route('worker.stock.delete-inventory', ['inventoryId' => $inventory->id]) }}" method="POST">
                        @csrf
                        <a href="{{ route('worker.stock.edit-inventory', ['inventoryId' => $inventory->id]) }}" 
                           class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded transition-colors" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="{{ route('worker.stock.inventory.increase-stock', ['inventoryId' => $inventory->id]) }}" 
                           class="px-2 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded transition-colors" title="Increase">
                            <i class="fa fa-plus"></i>
                        </a>
                        <a href="{{ route('worker.stock.inventory.decrease-stock', ['inventoryId' => $inventory->id]) }}" 
                           class="px-2 py-1 bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium rounded transition-colors" title="Decrease">
                            <i class="fa fa-minus"></i>
                        </a>
                        <a href="{{ route('worker.stock.inventory.logs', ['inventoryId' => $inventory->id]) }}" 
                           class="px-2 py-1 bg-cyan-500 hover:bg-cyan-600 text-white text-xs font-medium rounded transition-colors" title="History">
                            <i class="fa fa-history"></i>
                        </a>
                        <button type="submit" class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded transition-colors" title="Delete">
                            <i class="fa fa-trash"></i>
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
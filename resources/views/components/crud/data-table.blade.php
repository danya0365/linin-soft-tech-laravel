{{--
    Data Table Component
    
    Usage:
    <x-crud.data-table 
        :headers="['No', 'Name', 'Photo', 'Maximum Weight', '']"
        :data="$dryerMachines"
        :columns="['name', 'photo', 'maximum_weight']"
        resource="dryer-machines"
        :startIndex="$i"
    />
    
    Props:
    - headers: Array of table header labels
    - data: Collection of data items
    - columns: Array of column names to display from data
    - resource: Resource name for routing (e.g., 'dryer-machines')
    - startIndex: Starting index for numbering (optional, default: 0)
    - showActions: Show action buttons column (default: true)
--}}

@props([
    'headers' => [],
    'data',
    'columns' => [],
    'resource',
    'startIndex' => 0,
    'showActions' => true
])

<div class="overflow-x-auto rounded-lg shadow">
    <table class="w-full text-sm text-left border-collapse bg-white dark:bg-gray-800">
        <thead class="bg-gray-100 dark:bg-gray-700 border-b-2 border-gray-300 dark:border-gray-600">
            <tr>
                @foreach($headers as $header)
                    <th class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-left">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($data as $index => $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                    {{-- Index Number --}}
                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                        {{ $startIndex + $index + 1 }}
                    </td>
                    
                    {{-- Data Columns --}}
                    @foreach($columns as $column)
                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                            @if(str_contains($column, 'photo') || str_contains($column, 'image'))
                                @if($item->$column)
                                    <img src="{{ asset($item->$column) }}" 
                                         alt="{{ $item->name ?? 'Image' }}" 
                                         class="h-10 w-10 rounded object-cover ring-2 ring-gray-200 dark:ring-gray-600"
                                         onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center hidden">
                                        <i class="fa fa-image text-gray-400 dark:text-gray-500"></i>
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">No image</span>
                                @endif
                            @else
                                {{ $item->$column ?? '-' }}
                            @endif
                        </td>
                    @endforeach
                    
                    {{-- Actions Column --}}
                    @if($showActions)
                        <td class="px-6 py-4">
                            <x-crud.action-buttons 
                                :model="$item"
                                :resource="$resource"
                            />
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fa fa-inbox text-4xl text-gray-300 dark:text-gray-600"></i>
                            <p>No data available</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Worker Data Table Component (TailwindCSS) --}}
{{-- Usage: <x-worker.data-table :headers="['Col1', 'Col2']" :paginator="$items"> table body </x-worker.data-table> --}}

@props([
    'headers' => [],
    'paginator' => null,
    'striped' => true,
    'hover' => true
])

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        @if(count($headers) > 0)
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                @foreach($headers as $header)
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        @endif
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            {{ $slot }}
        </tbody>
    </table>
</div>

@if($paginator)
<div class="mt-4">
    {!! $paginator->withQueryString()->links() !!}
</div>
@endif

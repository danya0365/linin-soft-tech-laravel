@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="เลือกลูกค้า" 
    subtitle="Select Customer" 
    icon="fa-solid fa-building-user"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.collect.select-employee')],
        ['label' => 'จัดเก็บ - เลือกลูกค้า']
    ]"
>
    <div class="space-y-6">
        @foreach ($customerGroups as $customerGroup)
        <x-worker.card :title="$customerGroup['name']">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @foreach ($customerGroup['customers'] as $customer)
                <a href="{{ route('worker.operation.collect.set-select-customer', ['operationId' => $operation['id'], 'customerId' => $customer['id']]) }}" 
                   class="group relative flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-transparent hover:border-amber-500 hover:bg-white dark:hover:bg-gray-700 shadow-sm hover:shadow-md transition-all duration-200"
                   style="min-height: 150px">
                    
                    <div class="mb-3 transform group-hover:scale-110 transition-transform duration-200">
                        <div class="w-16 h-16 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/30 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                            <i class="bi bi-building text-3xl"></i>
                        </div>
                    </div>
                    
                    <div class="text-center w-full">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate px-2">
                            {{ $customer['name'] }}
                        </h4>
                    </div>
                </a>
                @endforeach
            </div>
        </x-worker.card>
        @endforeach
    </div>
</x-worker.page>
@endsection
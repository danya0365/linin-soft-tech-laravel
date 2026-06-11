@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="เลือกลูกค้า" 
    subtitle="Select Customer" 
    icon="fa-solid fa-building-user"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.dry.select-employee')],
        ['label' => 'อบ - เลือกลูกค้า']
    ]"
>
    <div class="space-y-6">
        @foreach ($customerGroups as $customerGroup)
        <x-worker.card :title="$customerGroup['name']">
            <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @foreach ($customerGroup['customers'] as $customer)
                <a href="{{ route('worker.operation.dry.set-select-customer', ['operationId' => $operation['id'], 'customerId' => $customer['id']]) }}" 
                   class="group flex flex-col items-center justify-center p-4 h-full rounded-xl border-2 border-transparent bg-gray-50 dark:bg-gray-800 hover:border-amber-400 hover:bg-white dark:hover:bg-gray-700 shadow-sm hover:shadow-md transition-all duration-200"
                   style="min-height: 150px">
                    
                    <div class="mb-3 transform group-hover:scale-110 transition-transform duration-200">
                        <div class="w-16 h-16 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/30 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                            <i class="bi bi-building text-3xl"></i>
                        </div>
                    </div>
                    
                    <span class="text-center font-medium text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400">
                        {{ $customer['name'] }}
                    </span>
                </a>
                @endforeach
            </div>
        </x-worker.card>
        @endforeach
    </div>
</x-worker.page>
@endsection
@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="เลือกประเภทผ้า" 
    subtitle="Select Linen Product" 
    icon="fa-solid fa-shirt"
    :breadcrumbs="[
        ['label' => 'Operation', 'route' => route('worker.operation')],
        ['label' => 'พนักงาน: ' . $operation['employee']['name'], 'route' => route('worker.operation.wash.select-employee')],
        ['label' => 'ลูกค้า: ' . $operation['customer']['name'], 'route' => route('worker.operation.wash.select-customer', ['operationId' => $operation['id']])],
        ['label' => 'เครื่องซักผ้า: ' . $operation['washing_machine']['name'], 'route' => route('worker.operation.wash.select-washing-machine', ['operationId' => $operation['id']])],
        ['label' => 'เคสงาน: ' . $operationLinenProduct['linen_case']['name'], 'route' => route('worker.operation.wash.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']])],
        ['label' => 'เลือกประเภทผ้า']
    ]"
>
    <div class="space-y-6">
        @foreach ($linenTypes as $linenType)
        <x-worker.card :title="$linenType['name']">
            <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @foreach ($linenType['linen_products'] as $linenProduct)
                <div class="relative">
                    <input type="checkbox" class="btn-check hidden peer" value="{{ $linenProduct['id'] }}" id="linen-{{ $linenProduct['id'] }}" autocomplete="off">
                    <label for="linen-{{ $linenProduct['id'] }}" 
                           class="flex flex-col items-center justify-center p-4 h-full rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 cursor-pointer transition-all duration-200 hover:border-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/30 peer-checked:ring-2 peer-checked:ring-amber-200 dark:peer-checked:ring-amber-900 shadow-sm">
                        
                        <div class="mb-2 text-gray-400 peer-checked:text-amber-500">
                            <i class="fa-solid fa-shirt text-2xl"></i>
                        </div>

                        <span class="text-center font-medium text-gray-700 dark:text-gray-300 peer-checked:text-amber-700 dark:peer-checked:text-amber-400">
                            {{ $linenProduct['name'] }}
                        </span>

                        <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                            <i class="fa-solid fa-circle-check text-amber-500"></i>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>
        </x-worker.card>
        @endforeach
    </div>

    {{-- Hidden Submit/Cancel buttons mainly for JS logic if needed, though hidden in original too --}}
    <div class="hidden">
        <button class="submit" type="button">ยืนยัน</button>
    </div>
</x-worker.page>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') return;
    
    var linenProducts = {!! $linenProductJson !!};
    var submitUrl = '{{ route('worker.operation.wash.set-select-linen-product', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id'], 'linenProductId' => ':linenProductIds']) }}'
    
    $(function(){

        function submit(){
            var selectCheckbox = $('.btn-check:checkbox:checked').map(function() {
                return this.value;
            }).get();

            if (!selectCheckbox || selectCheckbox.length === 0) {
                // Swal.fire('กรุณาเลือก 1 อย่าง', '', 'error')
                return
            }

            var selectedLinenProducts = selectCheckbox.map(function(element){
                var product = linenProducts.find(linenProduct => linenProduct.id == element);
                return product ? product.name : '';
            }).join(', ');

            Swal.fire({
                title: `ยืนยันที่จะเลือก ${selectedLinenProducts}`,
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: 'ยืนยัน',
                denyButtonText: `ยกเลิก`,
                customClass: {
                    popup: 'dark:bg-gray-800 dark:text-white',
                    confirmButton: 'bg-amber-500 hover:bg-amber-600 text-white rounded-lg px-4 py-2',
                    denyButton: 'bg-gray-500 hover:bg-gray-600 text-white rounded-lg px-4 py-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    submitUrl = submitUrl.replace(':linenProductIds', selectCheckbox.join(','));
                    window.location = submitUrl
                } else {
                    // Uncheck if cancelled
                    $('.btn-check').prop('checked', false);
                }
            })
        }

        // Trigger on change to capture styling updates
        $(document).on('change', '.btn-check', function() {
            if(this.checked) {
                // Uncheck others if single select logic is implied by previous behavior (navigating away on select)
                // But map supports multiple. Original logic: click -> submit immediately.
                // So really it's single select behavior effectively unless they check multiple super fast.
                // We'll keep it as is: check -> submit.
                submit();
            }
        });

        // Keep original handlers just in case
        $('.submit').click(function(){
            submit();
        })
    })

});
</script>
@endpush
@endsection
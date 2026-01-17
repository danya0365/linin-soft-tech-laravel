@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation') }}">ปฏิบัติการ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.dry.select-employee') }}">พนักงาน: {{ $operation['employee']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.dry.select-customer', ['operationId' => $operation['id']]) }}">ลูกค้า: {{ $operation['customer']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.dry.select-dryer-machine', ['operationId' => $operation['id']]) }}">{{ $operation['dryer_machine']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.dry.employee-summary', ['operationId' => $operation['id']]) }}">สรุปข้อมูลการอบ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.dry.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}">{{ $operationLinenProduct['linen_case']['name'] }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">อบ - เลือกประเภทผ้า</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        @foreach ($linenTypes as $linenType)
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">{{ $linenType['name'] }}</div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach ($linenType['linen_products'] as $linenProduct)
                        <div class="col-sm-3 col-6">
                            <div class="d-grid gap-2">
                                <input type="checkbox" class="btn-check" value="{{ $linenProduct['id'] }}" id="linen-{{ $linenProduct['id'] }}" autocomplete="off">
                                <label class="btn btn-outline-secondary" for="linen-{{ $linenProduct['id'] }}">{{ $linenProduct['name'] }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="row g-2 mt-2" style="display: none">
                        <div class="col-md-6 offset-md-3">
                            <div class="row g-2 mt-2">
                                <div class="col-6">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary submit" type="button">ยืนยันที่เลือก</button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-danger" type="button">ยกเลิกที่เลือก</button>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<script>
var linenProducts = {!! $linenProductJson !!};
var submitUrl = '{{ route('worker.operation.dry.set-select-linen-product', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id'], 'linenProductId' => ':linenProductIds']) }}'
function getLinenProductWithId(id){
    return linenProducts.find(linenProduct => linenProduct.id == id)
}

$(function(){

    function submit(){
        var selectCheckbox = $('.btn-check:checkbox:checked').map(function() {
            return this.value;
        }).get();

        var selectedLinenProducts = selectCheckbox.map(function(element){
            return linenProducts.find(linenProduct => linenProduct.id == element).name
        })

        console.log('selectCheckbox', selectCheckbox);
        if (!selectCheckbox || selectCheckbox.length === 0) {
            Swal.fire('กรุณาเลือก 1 อย่าง', '', 'error')
            return
        }

        Swal.fire({
            title: `ยืนยันที่จะเลือก ${selectedLinenProducts}`,
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'ยืนยัน',
            denyButtonText: `ยกเลิก`,
            }).then((result) => {
            if (result.isConfirmed) {
                //Swal.fire('Saved!', '', 'success')
                submitUrl = submitUrl.replace(':linenProductIds', selectCheckbox);
                window.location = submitUrl
            } else if (result.isDenied) {
                //Swal.fire('Changes are not saved', '', 'info')
            }
        })
    }

    $('.submit').click(function(){
        submit();
    })

    $(".btn-check").click(function(){
        submit();
    })
})
</script>
@endsection
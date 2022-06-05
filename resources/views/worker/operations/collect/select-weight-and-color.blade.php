@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation') }}">ปฏิบัติการ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.collect.select-employee') }}">พนักงาน: {{ $operation['employee']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.collect.select-customer', ['operationId' => $operation['id']]) }}">ลูกค้า: {{ $operation['customer']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.collect.employee-summary', ['operationId' => $operation['id']]) }}">สรุปข้อมูลการจัดเก็บ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.collect.select-linen-case', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}">{{ $operationLinenProduct['linen_case']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.collect.select-linen-product', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}">{{ $operationLinenProduct['linen_product']['name'] }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">จัดเก็บ - แบบฟอร์ม Submit</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">น้ำหนักที่จัดเก็บและสี</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('worker.operation.collect.set-select-weight-and-color', ['operationId' => $operation['id'], 'operationLinenProductId' => $operationLinenProduct['id']]) }}"  role="form" enctype="multipart/form-data">
                        @csrf
                        {{ Form::hidden('collect_weight', $operationLinenProduct['collect_weight']) }}
                        {{ Form::hidden('collect_pack', $operationLinenProduct['collect_pack']) }}
                        {{ Form::hidden('color', $operationLinenProduct['color']) }}
                        <div class="box box-info padding-1">
                            <div class="box-body">
                                
                                <div class="row g-2">
                                    <div class="col-sm-4">
                                        <div class="row g-2">
                                            <div class="col-12 text-center">
                                                <h2>น้ำหนักกิโลกรัม</h2>
                                            </div>
                                            <div class="col-12">
                                                <div class="p-3 border bg-light" style="width: 100%">
                                                    <div class="rounded-3" style="text-align: right; font-size: 48px" id="pad-weight-result">
                                                        0
                                                    </div>
                                                </div>
                                            </div>
        
                                            @foreach ([7, 8, 9, 4, 5, 6, 1, 2, 3, '', 0, 'ลบ'] as $pad)
                                            <div class="col-4">
                                                <div class="border bg-light" style="width: 100%">
                                                    <div class="rounded-3 d-flex align-items-center justify-content-center pad-weight-number" style="font-size: 48px">
                                                        {{ $pad }}
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="row g-2">
                                            <div class="col-12 text-center">
                                                <h2>จำนวนแพ็ค</h2>
                                            </div>
                                            <div class="col-12">
                                                <div class="p-3 border bg-light" style="width: 100%">
                                                    <div class="rounded-3" style="text-align: right; font-size: 48px" id="pad-pack-result">
                                                        0
                                                    </div>
                                                </div>
                                            </div>
        
                                            @foreach ([7, 8, 9, 4, 5, 6, 1, 2, 3, '', 0, 'ลบ'] as $pad)
                                            <div class="col-4">
                                                <div class="border bg-light" style="width: 100%">
                                                    <div class="rounded-3 d-flex align-items-center justify-content-center pad-pack-number" style="font-size: 48px">
                                                        {{ $pad }}
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="row g-2">

                                            <div class="col-12 text-center">
                                                <h2>สี</h2>
                                            </div>
                                            <div class="col-12">
                                                <div class="p-3 border bg-light" style="width: 100%">
                                                    <div class="rounded-3" style="text-align: right; font-size: 48px" id="color-result">
                                                        &nbsp;
                                                    </div>
                                                </div>
                                            </div>

                                            @foreach (['red', 'blue', 'yellow', 'green', 'orange', 'purple', 'magenta', 'cyan', 'white', 'black', 'gray', 'brown'] as $color)
                                            <div class="col-4">
                                                <div class="border color-number" style="width: 100%; background-color: {{ $color }}">
                                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="font-size: 48px; opacity: 0" data-color="{{ $color }}">
                                                        &nbsp;
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="box-footer">
                                <div class="row g-2 mt-2">
                                    <div class="col-6">
                                        <div class="d-grid gap-2" style="min-height: 60px">
                                            <button class="btn btn-primary" type="submit">ยืนยัน</button>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-grid gap-2" style="min-height: 60px">
                                            <button class="btn btn-danger" type="button" onclick="location.reload()">คืนค่า</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function(){
    var collect_weight = $('[name=collect_weight]').val();
    var collect_pack = $('[name=collect_pack]').val();
    var selectColor = $('[name=color]').val();
    var setPadWeightResult = function(number){
        var numbers = number.split('.');
        if (numbers.length == 2) {
            number = numbers[0] + '.' + numbers[1];
            number = parseFloat(number);
        } else {
            number = parseInt(number);
        }
        if (isNaN(number)) {
            number = 0;
        }
        $("#pad-weight-result").html(number.toLocaleString());
        $('[name=collect_weight]').val(number);
    }
    setPadWeightResult(collect_weight);
    $('.pad-weight-number').click(function(){
        var padNumber =  $.trim($(this).text())
        padNumber = padNumber.replace(',', '');
        if ( padNumber == '') {
            return
        }
        collect_weight = collect_weight != '0' ? collect_weight + '' + padNumber : padNumber;
        if ( padNumber == 'ลบ') {
            collect_weight = '0';
        }
        setPadWeightResult(collect_weight);
    })


    var setPadPackResult = function(number){
        var numbers = number.split('.');
        if (numbers.length == 2) {
            number = numbers[0] + '.' + numbers[1];
            number = parseFloat(number);
        } else {
            number = parseInt(number);
        }
        if (isNaN(number)) {
            number = 0;
        }
        $("#pad-pack-result").html(number.toLocaleString());
        $('[name=collect_pack]').val(number);
    }
    setPadPackResult(collect_pack);
    $('.pad-pack-number').click(function(){
        var padNumber =  $.trim($(this).text())
        padNumber = padNumber.replace(',', '');
        if ( padNumber == '') {
            return
        }
        collect_pack = collect_pack != '0' ? collect_pack + '' + padNumber : padNumber;
        if ( padNumber == 'ลบ') {
            collect_pack = '0';
        }
        setPadPackResult(collect_pack);
    })

    var setColorResult = (color) => {
        $("#color-result").css('background-color', color);
        $('[name=color]').val(color);
        selectColor = $('[name=color]').val();
    };
    setColorResult(selectColor);
    $('.color-number').click(function(){
        var color = $.trim($(this).find('div').data('color'))
        setColorResult(color);
    })
})
</script>

@endsection
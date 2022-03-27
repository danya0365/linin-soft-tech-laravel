@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation') }}">ปฏิบัติการ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.select-job', ['jobId' => $job['id']]) }}">ลูกค้า: {{ $job['customer']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.select-job', ['jobId' => $job['id']]) }}">ตู้เก็บน้ำหนัก: {{ $job['job_group']['wet_weight'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.select-job', ['jobId' => $job['id']]) }}">รถเข็นน้ำหนัก: {{ $job['wet_weight'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.select-job', ['jobId' => $job['id']]) }}" style="color: {{ $job['color'] }}">สี: {{ $job['color'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.select-job', ['jobId' => $job['id']]) }}">{{ $job['job_case']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.select-job', ['jobId' => $job['id']]) }}">{{ $job['washing_machine']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.select-job', ['jobId' => $job['id']]) }}">{{ $job['linen_type']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.select-job', ['jobId' => $job['id']]) }}">{{ $job['dryer_machine']['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.operation.iron.select-employee', ['jobId' => $job['id']]) }}">พนักงาน: {{ $job['employee']['name'] }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">อบ - แบบฟอร์ม Submit</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">จำนวนชิ้นและสี</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('worker.operation.iron.submit', ['jobId' => $job['id'] ]) }}"  role="form" enctype="multipart/form-data">
                        @csrf
                        {{ Form::hidden('piece', $job['piece']) }}
                        {{ Form::hidden('color', $job['color']) }}
                        <div class="box box-info padding-1">
                            <div class="box-body">
                                
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <div class="row g-2">
                                            <div class="col-12 text-center">
                                                <h2>จำนวนชิ้น</h2>
                                            </div>
                                            <div class="col-12">
                                                <div class="p-3 border bg-light" style="width: 100%">
                                                    <div class="rounded-3" style="text-align: right; font-size: 48px" id="pad-result">
                                                        0
                                                    </div>
                                                </div>
                                            </div>
        
                                            @foreach ([7, 8, 9, 4, 5, 6, 1, 2, 3, '', 0, 'ลบ'] as $pad)
                                            <div class="col-4">
                                                <div class="border bg-light" style="width: 100%">
                                                    <div class="rounded-3 d-flex align-items-center justify-content-center pad-number" style="font-size: 48px">
                                                        {{ $pad }}
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
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
                                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="font-size: 48px; opacity: 0">
                                                        {{ $color }}
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
                                            <button class="btn btn-danger" type="button">ยกเลิก</button>
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
    var number = $('[name=piece]').val();
    var selectColor = $('[name=color]').val();
    var setPadResult = function(number){
        var numbers = number.split('.');
        if (numbers.length == 2) {
            number = numbers[0] + '.' + numbers[1];
            number = parseFloat(number);
            //numbers = `${number}`.split('.');
            //number = numbers[0].toLocaleString() + (numbers[1] ? '.' + numbers[1] : '');
        } else {
            number = parseInt(number);
        }
        if (isNaN(number)) {
            number = 0;
        }
        $("#pad-result").html(number.toLocaleString());
        $('[name=piece]').val(number);
    }
    setPadResult(number);
    $('.pad-number').click(function(){
        var padNumber =  $.trim($(this).text())
        padNumber = padNumber.replace(',', '');
        if ( padNumber == '') {
            return
        }
        number = number != '0' ? number + '' + padNumber : padNumber;
        if ( padNumber == 'ลบ') {
            number = '0';
        }
        setPadResult(number);
    })

    var setColorResult = (color) => {
        $("#color-result").css('background-color', color);
        $('[name=color]').val(color);
        selectColor = $('[name=color]').val();
    };
    setColorResult(selectColor);
    $('.color-number').click(function(){
        var color = $.trim($(this).find('div').text())
        setColorResult(color);
    })
})
</script>

@endsection
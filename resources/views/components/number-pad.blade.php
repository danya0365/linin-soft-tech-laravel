<input type="hidden" value="{{ $inputValue }}" name="{{ $inputName }}" />
<div class="row g-2">
    <div class="col-12 text-center">
        <h2>{{ $slot }}</h2>
    </div>
    <div class="col-12">
        <div class="p-3 border bg-light" style="width: 100%">
            <div class="rounded-3" style="text-align: right; font-size: 48px" id="pad-{{ $inputName }}-result">
                {{ $inputValue }}
            </div>
        </div>
    </div>

    @foreach ([7, 8, 9, 4, 5, 6, 1, 2, 3, '.', 0, 'ลบ'] as $pad)
    <div class="col-4">
        <div class="border bg-light" style="width: 100%">
            <div class="rounded-3 d-flex align-items-center justify-content-center pad-{{ $inputName }}-number" style="font-size: 48px">
                {{ $pad }}
            </div>
        </div>
    </div>
    @endforeach
</div>
@push('scripts')
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') return;
    $(function(){
        var {{ $inputName }} = $('[name={{ $inputName }}]').val();
        var setPadResult = function(number){
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
            $("#pad-{{ $inputName }}-result").html(number.toLocaleString());
            $('[name={{ $inputName }}]').val(number);
        }
        setPadResult({{ $inputName }});
        $('.pad-{{ $inputName }}-number').click(function(){
            var padNumber =  $.trim($(this).text())
            padNumber = padNumber.replace(',', '');
            if ( padNumber == '') {
                return
            }
            {{ $inputName }} = {{ $inputName }} != '0' ? {{ $inputName }} + '' + padNumber : padNumber;
            if ( padNumber == 'ลบ') {
                {{ $inputName }} = '0';
            }
            setPadResult({{ $inputName }});
        })
    })
});
</script>
@endpush
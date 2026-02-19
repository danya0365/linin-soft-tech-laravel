<input type="hidden" value="{{ $inputValue }}" name="{{ $inputName }}" />
<div class="grid grid-cols-12 gap-2">
    <div class="col-span-12 text-center">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ $slot }}</h2>
    </div>
    <div class="col-span-12">
        <div class="p-3 border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 w-full rounded-lg">
            <div class="rounded-lg text-right text-5xl h-[72px] flex items-center justify-end px-2" id="pad-{{ $inputName }}-result">
                {{ $inputValue }}
            </div>
        </div>
    </div>

    @foreach ([7, 8, 9, 4, 5, 6, 1, 2, 3, '.', 0, 'ลบ'] as $pad)
    <div class="col-span-4">
        <div class="border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 w-full rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition shadow-sm hover:shadow">
            <div class="rounded-lg flex items-center justify-center h-[72px] pad-{{ $inputName }}-number text-5xl font-medium" >
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
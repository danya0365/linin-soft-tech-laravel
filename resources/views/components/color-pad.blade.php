<input type="hidden" value="{{ $inputValue }}" name="{{ $inputName }}" />
<div class="row g-2">
    <div class="col-12 text-center">
        <h2>{{ $slot }}</h2>
    </div>
    <div class="col-12">
        <div class="p-3 border bg-light" style="width: 100%">
            <div class="rounded-3" style="text-align: right; font-size: 48px" id="pad-{{ $inputName }}-result">
                &nbsp;
            </div>
        </div>
    </div>

    @foreach (['red', 'blue', 'yellow', 'green', 'orange', 'purple', 'magenta', 'cyan', 'white', 'black', 'gray', 'brown'] as $color)
    <div class="col-4">
        <div class="border color-number" style="width: 100%; background-color: {{ $color }}">
            <div class="rounded-3 d-flex align-items-center justify-content-center pad-{{ $inputName }}-number" style="font-size: 48px; opacity: 0"  data-color="{{ $color }}">
                &nbsp;
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
        var setPadResult = (color) => {
            $("#pad-{{ $inputName }}-result").css('background-color', color);
            $('[name={{ $inputName }}]').val(color);
        };
        setPadResult({{ $inputName }});
        $('.color-number').click(function(){
            var padColor = $.trim($(this).find('div').data('color'))
            {{ $inputName }} = padColor;
            setPadResult({{ $inputName }});
        })
    })
});
</script>
@endpush
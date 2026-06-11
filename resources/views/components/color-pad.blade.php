<input type="hidden" value="{{ $inputValue }}" name="{{ $inputName }}" />
<div class="grid grid-cols-12 gap-2">
    <div class="col-span-12 text-center">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ $slot }}</h2>
    </div>
    <div class="col-span-12">
        <div class="p-3 border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 w-full rounded-lg">
            <div class="rounded-lg text-right text-5xl h-[72px] flex items-center justify-end px-2 text-gray-900 dark:text-gray-100" id="pad-{{ $inputName }}-result">
                &nbsp;
            </div>
        </div>
    </div>

    @foreach (['red', 'blue', 'yellow', 'green', 'orange', 'purple', 'magenta', 'cyan', 'white', 'black', 'gray', 'brown'] as $color)
    <div class="col-span-4">
        <div class="border border-gray-200 dark:border-gray-600 cursor-pointer color-number rounded-lg overflow-hidden w-full shadow-sm hover:shadow transition-shadow" style="background-color: {{ $color }}">
            <div class="flex items-center justify-center h-[72px] pad-{{ $inputName }}-number text-5xl opacity-0 hover:opacity-100 transition-opacity duration-200 text-white mix-blend-difference font-bold" data-color="{{ $color }}">
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
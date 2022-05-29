@foreach($links as $link )
   @if ( $loop->last )
    <li class="breadcrumb-item active">
        {{ $link['name'] }}
    </li>
   @else
    <li class="breadcrumb-item">
        <a href="{{ $link['link'] }}">{{ $link['name'] }}</a>
    </li>
   @endif
@endforeach
@extends('layouts.app')

@section('template_title')
    {{ $truck->name ?? 'Show Truck' }}
@endsection

@section('content')
    <div class="container">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Admin') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Truck') }}</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Truck</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('trucks.create-note', ['id' => $truck->id]) }}"> Add Note</a>
                            <a class="btn btn-primary" href="{{ route('trucks.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $truck->name }}
                        </div>
                        <div class="form-group">
                            <strong>Photo:</strong>
                            {{ $truck->photo }}
                        </div>
                        <div class="form-group">
                            <strong>Plate Number:</strong>
                            {{ $truck->plate_number }}
                        </div>
                        <div class="form-group">
                            <strong>Operation Id:</strong>
                            {{ $truck->operation_id }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Note') }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Tag Filter Section --}}
                        @if(count($machineTags) > 0)
                        <div class="mb-4">
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span class="text-muted me-2">🏷️ กรองตาม Tag:</span>
                                <a href="{{ route('trucks.show', $truck->id) }}" 
                                   class="btn btn-sm {{ !$selectedTag ? 'btn-dark' : 'btn-outline-dark' }}">
                                    ทั้งหมด
                                </a>
                                @foreach($machineTags as $tag)
                                    <a href="{{ route('trucks.show', ['truck' => $truck->id, 'tag' => $tag]) }}" 
                                       class="btn btn-sm {{ $selectedTag == $tag ? 'btn-primary' : 'btn-outline-secondary' }}">
                                        #{{ $tag }}
                                    </a>
                                @endforeach
                            </div>
                            @if($selectedTag)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        กำลังแสดง: <strong>#{{ $selectedTag }}</strong>
                                        <a href="{{ route('trucks.show', $truck->id) }}" class="text-danger ms-2">
                                            <i class="fa fa-times"></i> ล้าง filter
                                        </a>
                                    </small>
                                </div>
                            @endif
                        </div>
                        @endif

                        {{-- Notes List --}}
                    @forelse ($notes as $note)
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">ค่าใช้จ่าย: {{ number_format($note->cost, 2) }} ฿</h5>
                                    @if($note->tags && count($note->tags) > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($note->tags as $tag)
                                                <a href="{{ route('trucks.show', ['truck' => $truck->id, 'tag' => $tag]) }}" 
                                                   class="badge text-white text-decoration-none" 
                                                   style="background-color: {{ \App\Models\Note::getTagColor($tag) }}; cursor: pointer; transition: transform 0.2s;"
                                                   title="คลิกเพื่อกรองตาม #{{ $tag }}"
                                                   onmouseover="this.style.transform='scale(1.1)'" 
                                                   onmouseout="this.style.transform='scale(1)'">
                                                    #{{ $tag }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <p class="card-text">{{ $note->message }}</p>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <small class="text-muted">วันที่: {{ $note->created_at->format('Y-m-d') }}</small>
                                </li>
                            </ul>
                            <div class="card-body">
                                <form action="{{ route('notes.destroy',$note->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i> Delete</button>
                                </form>
                            </div>
                            @if ( $note->image_url != '' )
                            <img src="{{ asset($note->image_url) }}" class="card-img-bottom" alt="{{ asset($note->image_url) }}">
                            @endif
                        </div>
                    @empty
                        <div class="alert alert-info">
                            @if($selectedTag)
                                ไม่พบบันทึกที่มี tag "#{{ $selectedTag }}"
                            @else
                                ยังไม่มีบันทึก
                            @endif
                        </div>
                    @endforelse
                    </div>
                </div>
                {!! $notes->appends(['tag' => $selectedTag])->links() !!}
            </div>
        </div>
    </div>
@endsection

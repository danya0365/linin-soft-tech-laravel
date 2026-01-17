@extends('layouts.app')

@section('template_title')
    {{ $dryerMachine->name ?? 'Show Dryer Machine' }}
@endsection

@section('content')
    <section class="content container">
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
                            <span class="card-title">Show Dryer Machine</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('dryer-machines.create-note', ['id' => $dryerMachine->id]) }}"> Add Note</a>
                            <a class="btn btn-primary" href="{{ route('dryer-machines.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $dryerMachine->name }}
                        </div>
                        <div class="form-group">
                            <strong>Photo:</strong>
                            {{ $dryerMachine->photo }}
                        </div>
                        <div class="form-group">
                            <strong>Maximum Weight:</strong>
                            {{ $dryerMachine->maximum_weight }}
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
                                <a href="{{ route('dryer-machines.show', $dryerMachine->id) }}" 
                                   class="btn btn-sm {{ !$selectedTag ? 'btn-dark' : 'btn-outline-dark' }}">
                                    ทั้งหมด
                                </a>
                                @foreach($machineTags as $tag)
                                    <a href="{{ route('dryer-machines.show', ['dryer_machine' => $dryerMachine->id, 'tag' => $tag]) }}" 
                                       class="btn btn-sm {{ $selectedTag == $tag ? 'btn-primary' : 'btn-outline-secondary' }}">
                                        #{{ $tag }}
                                    </a>
                                @endforeach
                            </div>
                            @if($selectedTag)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        กำลังแสดง: <strong>#{{ $selectedTag }}</strong>
                                        <a href="{{ route('dryer-machines.show', $dryerMachine->id) }}" class="text-danger ms-2">
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
                                    @if($note->tag)
                                        <a href="{{ route('dryer-machines.show', ['dryer_machine' => $dryerMachine->id, 'tag' => $note->tag]) }}" 
                                           class="badge text-white text-decoration-none" 
                                           style="background-color: {{ $note->tag_color }}; cursor: pointer; transition: transform 0.2s;"
                                           title="คลิกเพื่อกรองตาม #{{ $note->tag }}"
                                           onmouseover="this.style.transform='scale(1.1)'" 
                                           onmouseout="this.style.transform='scale(1)'">
                                            #{{ $note->tag }}
                                        </a>
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
    </section>
@endsection

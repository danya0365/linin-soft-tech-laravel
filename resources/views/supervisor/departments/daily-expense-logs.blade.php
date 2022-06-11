@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('supervisor') }}">Supervisor</a></li>
            <li class="breadcrumb-item"><a href="{{ route('supervisor.department') }}">{{ __('Department') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">ประวัติค่าใช้จ่ายรายวัน</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">

            @if ($message = Session::get('success'))
            <div class="alert alert-success mb-2">
                {{ $message }}
            </div>
            @endif
            
            <div class="card">
                <div class="card-header">ประวัติค่าใช้จ่ายรายวัน</div>
                <div class="card-body">
 
                    <form class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ request()->url() }}" method="GET">

                        <div class="col-12">
                            <div class="input-group">
                                <label class="input-group-text" for="department_id">แผนก</label>
                                <select class="form-select" id="department_id" name="department_id" onchange="this.form.submit()">
                                    <option value="">แสดงทั้งหมด - Show All</option>
                                    @foreach ( $departments as $key => $department )
                                    <option value="{{ $department['id'] }}" {{ $departmentSelected == $department['id'] ? 'selected' : '' }}>{{ $department['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="input-group">
                                <input type="date" name="date_start_at" value="{{ $dateStartAt }}" class="form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="date" name="date_end_at" value="{{ $dateEndAt }}" class="form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group">
                                <label class="input-group-text" for="sort_order">เรียงโดย</label>
                                <select class="form-select" id="sort_order" name="sort_order" onchange="this.form.submit()">
                                    @foreach ( $sortOrders as $sortOrder )
                                    <option value="{{ $sortOrder['var'] }}" {{ $sortOrderSelected == $sortOrder['var'] ? 'selected' : '' }}>{{ $sortOrder['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('supervisor.department.daily-expense-log') }}" role="button" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>

                    @foreach ($departmentDailyCostLogs as $departmentDailyCostLog)
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="card-title">แผนก: {{ $departmentDailyCostLog->department->name  }}</h5>
                            <p class="card-text">{{ $departmentDailyCostLog->message }}</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                วันที่: {{ $departmentDailyCostLog->daily_date }}
                            </li>
                            <li class="list-group-item">
                                จำนวน: {{ number_format($departmentDailyCostLog->cost) }}
                            </li>
                            <li class="list-group-item">
                                <small class="text-muted">บันทึกเมื่อ: {{ $departmentDailyCostLog->created_at->format('Y-m-d') }}</small>
                            </li>
                        </ul>
                        <div class="card-body">
                            <form class="delete-form" action="{{ route('supervisor.department.delete-daily-expense-log', ['id' => $departmentDailyCostLog->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-fw fa-trash"></i> Delete</button>
                            </form>
                        </div>
                        @if ( $departmentDailyCostLog->image_url != '' )
                        <img src="{{ asset($departmentDailyCostLog->image_url) }}" class="card-img-bottom" alt="{{ asset($departmentDailyCostLog->image_url) }}">
                        @endif
                    </div>
                    @endforeach

                </div>
                <div class="card-footer">
                    {!! $departmentDailyCostLogs->withQueryString()->links() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    $('.delete-form').on('submit', function(e){
        if (!confirm("Are you sure?")) {
            return false;
        }
        return true
    })
});
 </script>
@endsection
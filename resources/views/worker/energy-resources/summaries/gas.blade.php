@extends('layouts.worker')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('worker') }}">Worker</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.energy-resource') }}">{{ __('Energy Resource') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('worker.energy-resource.logs') }}">{{ __('Logs') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">สถิติการใช้แก๊ส</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <div class="col-md-12 m-2">
            <div class="card">
                <div class="card-header">ปริมาณการใช้แก๊สล่าสุด -</div>
                <div class="card-body">
 
                    <form class="row row-cols-lg-auto g-3 align-items-center mb-2" action="{{ request()->url() }}" method="GET">
                        
                        <div class="col-12">
                            <div class="input-group">
                                <input type="date" name="date_start_at" value="{{ $dateStartAt }}" class="form-control" placeholder="วันที่เริ่ม" aria-label="วันที่เริ่ม">
                                <span class="input-group-text"> ถึง </span>
                                <input type="date" name="date_end_at" value="{{ $dateEndAt }}" class="form-control" placeholder="วันที่สิ้นสุด" aria-label="วันที่สิ้นสุด">
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit Filter By Date</button>
                            <a href="{{ request()->url() }}" role="button" class="btn btn-outline-secondary">Reset Filter</a>
                        </div>
                    </form>

                </div>
                <div class="card-body text-center">
                    <div id="energy-chart" style="min-width: 400px; height: 400px; margin: 0 auto">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

$(function(){
    var energyDaysSummary = @json(App\Managers\HighChartManager::getEnergyDaysSummary());
    $.energyDaysChart({ 'renderTo': 'energy-chart', 'data': energyDaysSummary});
})
</script>
@endsection
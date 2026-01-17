@extends('layouts.user-customer')

@section('content')

<div class="container">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">User Customer</li>
        </ol>
    </nav>
    
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                {{-- Header with gradient background --}}
                <div class="card-header text-white text-center py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="mb-3">
                        <i class="fa-solid fa-tools" style="font-size: 4rem; opacity: 0.9;"></i>
                    </div>
                    <h2 class="mb-0 fw-bold">🚧 Under Construction 🚧</h2>
                    <p class="mb-0 mt-2 opacity-75">กำลังพัฒนา</p>
                </div>
                
                <div class="card-body text-center py-5">
                    {{-- Main message --}}
                    <div class="mb-4">
                        <h4 class="text-secondary mb-3">
                            <i class="fa-solid fa-gear fa-spin me-2"></i>
                            ฟีเจอร์นี้อยู่ระหว่างการพัฒนา
                        </h4>
                        <p class="text-muted lead">
                            เรากำลังพัฒนาระบบ User Customer ในเฟสถัดไป<br>
                            กรุณารอติดตามการอัปเดตเร็วๆ นี้
                        </p>
                    </div>
                    
                    {{-- Feature preview --}}
                    <div class="row g-3 mb-4 justify-content-center">
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <i class="fa-solid fa-chart-line text-primary mb-2" style="font-size: 2rem;"></i>
                                <p class="mb-0 small text-muted">ดูรายงานสรุป</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <i class="fa-solid fa-file-invoice text-success mb-2" style="font-size: 2rem;"></i>
                                <p class="mb-0 small text-muted">ตรวจสอบบิล</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <i class="fa-solid fa-history text-warning mb-2" style="font-size: 2rem;"></i>
                                <p class="mb-0 small text-muted">ประวัติงาน</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Status badge --}}
                    <div class="mb-4">
                        <span class="badge rounded-pill px-4 py-2" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); font-size: 0.9rem;">
                            <i class="fa-solid fa-clock me-1"></i> Coming Soon - Phase 2
                        </span>
                    </div>
                    
                    {{-- Back button --}}
                    <a href="{{ url('/') }}" class="btn btn-outline-primary btn-lg px-5 rounded-pill">
                        <i class="fa-solid fa-home me-2"></i> กลับหน้าหลัก
                    </a>
                </div>
                
                {{-- Footer --}}
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted">
                        <i class="fa-solid fa-info-circle me-1"></i>
                        หากมีข้อสงสัย กรุณาติดต่อทีมงาน LinenSoftTech
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
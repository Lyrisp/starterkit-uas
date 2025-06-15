@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-4 text-primary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-newspaper fa-3x mb-3"></i>
                    <h2 class="fw-bold">{{ $total_berita }}</h2>
                    <p class="mb-0">Total Berita</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-3x mb-3"></i>
                    <h2 class="fw-bold">{{ $pending_berita }}</h2>
                    <p class="mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h2 class="fw-bold">{{ $approved_berita }}</h2>
                    <p class="mb-0">Approved</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h2 class="fw-bold">{{ $total_user }}</h2>
                    <p class="mb-0">Total User</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h4>
        </div>
        <div class="card-body">
            <div class="row g-3 justify-content-center">
                <div class="col-md-6">
                    <a href="{{ route('berita.index') }}" class="btn btn-outline-primary btn-lg w-100 py-3">
                        <i class="fas fa-list fa-2x d-block mb-2"></i>
                        Kelola Berita
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('berita.create') }}" class="btn btn-outline-success btn-lg w-100 py-3">
                        <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                        Tambah Berita
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.btn:hover {
    transform: translateY(-2px);
}
</style>
@endsection
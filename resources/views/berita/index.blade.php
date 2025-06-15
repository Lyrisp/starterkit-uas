@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-5 text-primary">
                <i class="fas fa-newspaper me-2"></i>Daftar Berita
            </h1>
            <p class="text-muted">Kelola dan pantau semua berita</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Dashboard
            </a>
            @if(in_array(Auth::user()->role, ['admin', 'wartawan']))
                <a href="{{ route('berita.create') }}" class="btn btn-success btn-lg">
                    <i class="fas fa-plus me-2"></i>Tambah Berita
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search & Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari berita..." id="searchInput">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-primary w-100" onclick="resetFilter()">
                        <i class="fas fa-refresh me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Berita Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($beritas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th width="250px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($beritas as $berita)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($berita->gambar)
                                                <img src="{{ asset('storage/'.$berita->gambar) }}" 
                                                     class="rounded me-3" width="50" height="50" style="object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ Str::limit($berita->judul, 40) }}</div>
                                                <small class="text-muted">{{ Str::limit($berita->konten, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 32px; height: 32px;">
                                                <i class="fas fa-user fa-sm"></i>
                                            </div>
                                            {{ $berita->user->name }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($berita->status === 'approved')
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-check me-1"></i>Approved
                                            </span>
                                        @elseif($berita->status === 'rejected')
                                            <span class="badge bg-danger px-3 py-2">
                                                <i class="fas fa-times me-1"></i>Rejected
                                            </span>
                                        @else
                                            <span class="badge bg-warning px-3 py-2">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $berita->created_at->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $berita->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- View Button -->
                                            <a href="{{ route('berita.show', $berita->id) }}" 
                                               class="btn btn-sm btn-outline-info" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Edit & Delete - Admin/Wartawan (dengan validasi) -->
                                            @if((Auth::user()->role === 'admin') || 
                                                (Auth::user()->role === 'wartawan' && Auth::id() === $berita->user_id))
                                                <a href="{{ route('berita.edit', $berita->id) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" title="Hapus"
                                                        onclick="deleteBerita({{ $berita->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif

                                            <!-- Approve & Reject - Admin/Editor (hanya untuk status pending) -->
                                            @if(in_array(Auth::user()->role, ['admin', 'editor']) && $berita->status === 'pending')
                                                <!-- Approve Button -->
                                                <form action="{{ route('berita.approve', $berita->id) }}" method="POST" style="display:inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approve"
                                                            onclick="return confirm('Apakah Anda yakin ingin approve berita ini?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>

                                                <!-- Reject Button -->
                                                <form action="{{ route('berita.reject', $berita->id) }}" method="POST" style="display:inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Reject"
                                                            onclick="return confirm('Apakah Anda yakin ingin reject berita ini?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(method_exists($beritas, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        {{ $beritas->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-5x text-muted mb-3"></i>
                    <h4 class="text-muted">Belum Ada Berita</h4>
                    <p class="text-muted">Mulai dengan menambahkan berita pertama</p>
                    @if(in_array(Auth::user()->role, ['admin', 'wartawan']))
                        <a href="{{ route('berita.create') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-plus me-2"></i>Tambah Berita Sekarang
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}
.btn-group .btn {
    transition: all 0.2s;
}
.btn-group .btn:hover {
    transform: translateY(-1px);
}
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
</style>

<script>
function deleteBerita(id) {
    if (confirm('Yakin ingin menghapus berita ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/berita/${id}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function resetFilter() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    // Show all rows
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => row.style.display = '');
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Status filter
document.getElementById('statusFilter').addEventListener('change', function() {
    const status = this.value;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (!status) {
            row.style.display = '';
        } else {
            const badge = row.querySelector('.badge');
            let rowStatus = 'pending';
            if (badge.textContent.toLowerCase().includes('approved')) {
                rowStatus = 'approved';
            } else if (badge.textContent.toLowerCase().includes('rejected')) {
                rowStatus = 'rejected';
            }
            row.style.display = rowStatus === status ? '' : 'none';
        }
    });
});
</script>
@endsection
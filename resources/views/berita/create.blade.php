@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-5 text-success">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Berita Baru
                    </h1>
                    <p class="text-muted">Buat dan publikasikan berita baru</p>
                </div>
                <a href="{{ route('berita.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>

            <form action="{{ route('berita.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Main Content -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Konten Berita</h5>
                            </div>
                            <div class="card-body">
                                <!-- Judul -->
                                <div class="mb-4">
                                    <label for="judul" class="form-label fw-bold">
                                        Judul Berita <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-lg @error('judul') is-invalid @enderror" 
                                           id="judul" name="judul" value="{{ old('judul') }}" 
                                           placeholder="Masukkan judul berita yang menarik..." required>
                                    @error('judul')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Konten -->
                                <div class="mb-4">
                                    <label for="konten" class="form-label fw-bold">
                                        Konten Berita <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('konten') is-invalid @enderror" 
                                              id="konten" name="konten" rows="10" required
                                              placeholder="Tulis konten berita Anda di sini...">{{ old('konten') }}</textarea>
                                    @error('konten')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Kategori -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Kategori</h6>
                            </div>
                            <div class="card-body">
                                <select class="form-select @error('kategori_id') is-invalid @enderror" 
                                        name="kategori_id" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}" 
                                                {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Gambar -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-image me-2"></i>Gambar Utama</h6>
                            </div>
                            <div class="card-body">
                                <input type="file" class="form-control @error('gambar') is-invalid @enderror" 
                                       name="gambar" accept="image/*" onchange="previewImage(this)">
                                <div class="form-text">Format: JPG, PNG, JPEG. Maksimal 2MB</div>
                                @error('gambar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <!-- Preview -->
                                <div id="imagePreview" class="mt-3" style="display: none;">
                                    <img id="preview" src="" alt="Preview" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-save me-2"></i>Simpan Berita
                                    </button>
                                    <a href="{{ route('berita.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
#konten {
    min-height: 200px;
}
</style>

<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Auto resize textarea
document.getElementById('konten').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});
</script>
@endsection
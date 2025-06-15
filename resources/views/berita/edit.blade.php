@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Berita</h1>

    <form action="{{ route('berita.update', $berita->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Judul</label>
            <input type="text" name="judul" class="form-control" value="{{ $berita->judul }}" required>
        </div>

        <div class="mb-3">
            <label>Konten</label>
            <textarea name="konten" class="form-control" rows="5" required>{{ $berita->konten }}</textarea>
        </div>

        <div class="mb-3">
            <label>Gambar (biarkan kosong jika tidak diubah)</label>
            <input type="file" name="gambar" class="form-control">
        </div>

        <button type="submit" class="btn btn-warning">Update</button>
        <a href="{{ route('berita.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

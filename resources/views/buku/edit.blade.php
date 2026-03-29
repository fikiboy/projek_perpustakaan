@extends('layouts.app')

@section('content')
<div class="container mt-4 animate__animated animate__fadeIn">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4 text-primary">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Edit Buku
                    </h4>
                    
                    <form action="{{ route('buku.update', $buku->BukuID) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Judul Buku</label>
                            <input type="text" name="Judul" class="form-control rounded-pill" value="{{ $buku->Judul }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Penulis</label>
                                <input type="text" name="Penulis" class="form-control rounded-pill" value="{{ $buku->Penulis }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Penerbit</label>
                                <input type="text" name="Penerbit" class="form-control rounded-pill" value="{{ $buku->Penerbit }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Tahun Terbit</label>
                                <input type="number" name="TahunTerbit" class="form-control rounded-pill" value="{{ $buku->TahunTerbit }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Kategori Buku</label>
                                <select name="KategoriID" class="form-select rounded-pill">
                                    <option value="">-- Pilih Kategori --</option>
                                    @php 
                                        $daftarKategori = [];
                                        if(class_exists('App\Models\KategoriBuku')) {
                                            $daftarKategori = \App\Models\KategoriBuku::all();
                                        }
                                    @endphp

                                    @foreach($daftarKategori as $kat)
                                        <option value="{{ $kat->KategoriID }}" 
                                            {{ (isset($buku) && $buku->KategoriID == $kat->KategoriID) ? 'selected' : '' }}>
                                            {{ $kat->NamaKategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label small fw-bold">Stok Buku</label>
                                <input type="number" name="Stok" class="form-control rounded-pill" value="{{ $buku->Stok }}" required min="0">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Ganti Cover (Opsional)</label>
                            <input type="file" name="Cover" class="form-control rounded-pill">
                            <small class="text-muted d-block mt-1 ms-2">Biarkan kosong jika tidak ingin mengganti gambar.</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold flex-grow-1">
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-4 border">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
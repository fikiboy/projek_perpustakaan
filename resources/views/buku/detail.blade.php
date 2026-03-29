@extends('layouts.app')

@section('content')
<div class="container py-5 animate__animated animate__fadeIn">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-sticky" style="top: 20px;">
                <img src="{{ $buku->Cover ? asset('covers/' . $buku->Cover) : 'https://ui-avatars.com/api/?name='.urlencode($buku->Judul) }}" 
                     class="img-fluid w-100" alt="{{ $buku->Judul }}" style="height: 500px; object-fit: cover;">
            </div>
        </div>

        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h1 class="fw-bold text-dark mb-1">{{ $buku->Judul }}</h1>
                    <p class="text-primary fs-5 fw-medium mb-0">{{ $buku->Penulis }}</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-primary border rounded-pill px-3 py-2">
                        <i class="fa-solid fa-bookmark me-1"></i> {{ $buku->kategori->NamaKategori ?? 'Umum' }}
                    </span>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-4">
                    <div class="bg-light p-3 rounded-4 text-center border-0 shadow-sm">
                        <small class="text-muted d-block">Penerbit</small>
                        <span class="fw-bold small">{{ $buku->Penerbit }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-light p-3 rounded-4 text-center border-0 shadow-sm">
                        <small class="text-muted d-block">Tahun</small>
                        <span class="fw-bold small">{{ $buku->TahunTerbit }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-light p-3 rounded-4 text-center border-0 shadow-sm">
                        <small class="text-muted d-block">Stok</small>
                        <span class="fw-bold small {{ $buku->Stok > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $buku->Stok }} Buku
                        </span>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mt-4">Sinopsis</h5>
            <p class="text-muted lh-lg mb-4">{{ $buku->Deskripsi ?? 'Sinopsis tidak tersedia untuk buku ini.' }}</p>

            <div class="d-flex gap-2 mb-5">
                @if($buku->Stok > 0)
                <form action="{{ route('buku.pinjam.store', $buku->BukuID) }}" method="POST" class="flex-grow-1">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm py-3">
                        <i class="fa-solid fa-book-reader me-2"></i> Pinjam Sekarang
                    </button>
                </form>
                @endif

                <form action="{{ route('buku.favorit', $buku->BukuID) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-lg rounded-pill px-4 shadow-sm {{ $buku->isFavorit ? 'btn-danger' : 'btn-outline-danger' }}" style="height: 62px; transition: 0.3s;">
                        <i class="fa-{{ $buku->isFavorit ? 'solid' : 'regular' }} fa-heart"></i>
                    </button>
                </form>

                <button class="btn btn-warning btn-lg px-4 rounded-pill text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUlasan" style="height: 62px;">
                    <i class="fa-solid fa-star me-1"></i> Beri Ulasan
                </button>
            </div>

            <hr class="my-5 opacity-25">

            <h5 class="fw-bold mb-4">Ulasan Pembaca ({{ $buku->ulasan ? $buku->ulasan->count() : 0 }})</h5>
            <div class="row">
                @if($buku->ulasan && $buku->ulasan->count() > 0)
                    @foreach($buku->ulasan as $u)
                    <div class="col-12 mb-3">
                        <div class="card border-0 bg-light rounded-4 p-3 shadow-sm">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold text-dark small">
                                    <i class="fa-solid fa-circle-user me-2 text-primary"></i>{{ $u->user->NamaLengkap ?? 'Pembaca' }}
                                </span>
                                <div class="text-warning small">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="fa-{{ $i <= $u->Rating ? 'solid' : 'regular' }} fa-star"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-muted mb-0 small" style="font-style: italic;">"{{ $u->Ulasan }}"</p>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-4 bg-light rounded-4 border-dashed">
                        <p class="text-muted mb-0 small">Belum ada ulasan. Jadilah yang pertama memberikan ulasan!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUlasan" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">Berikan Ulasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ulasan.store') }}" method="POST">
                @csrf
                <input type="hidden" name="BukuID" value="{{ $buku->BukuID }}">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Rating Bintang</label>
                        <select name="Rating" class="form-select border-0 bg-light rounded-pill py-2 px-3 shadow-none">
                            <option value="5">⭐⭐⭐⭐⭐ (Sangat Puas)</option>
                            <option value="4">⭐⭐⭐⭐ (Puas)</option>
                            <option value="3">⭐⭐⭐ (Cukup)</option>
                            <option value="2">⭐⭐ (Kurang)</option>
                            <option value="1">⭐ (Buruk)</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">Ulasan Anda</label>
                        <textarea name="Ulasan" class="form-control border-0 bg-light rounded-4 py-3 px-3 shadow-none" 
                                  rows="4" placeholder="Apa pendapatmu tentang buku ini?" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">Kirim Ulasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important;
        border: 1px solid #0d6efd !important;
    }
    .border-dashed { border: 2px dashed #dee2e6; }
    
    /* Animasi sederhana untuk tombol favorit */
    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: white;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if(window.location.hash === "#form-ulasan") {
            var ulasanModal = new bootstrap.Modal(document.getElementById('modalUlasan'));
            ulasanModal.show();
        }
    });
</script>
@endsection
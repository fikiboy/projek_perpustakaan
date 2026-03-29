@extends('layouts.app')

@section('content')
<div class="container mt-4 animate__animated animate__fadeIn">
    
    <div class="row align-items-center mb-5">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Halo, {{ Auth::user()->NamaLengkap }}! 👋</h2>
            <p class="text-muted">Jelajahi dunia melalui ribuan koleksi buku di iBooku.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-inline-block text-start" style="min-width: 220px;">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle p-3 rounded-4 me-3">
                        <i class="fa-solid fa-book-open text-primary fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Buku Dipinjam</small>
                        <h4 class="fw-bold mb-0 text-primary">{{ $jumlahPinjam }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-5 align-items-center">
        <div class="col-lg-7">
            <form action="{{ route('dashboard') }}" method="GET" class="position-relative">
                <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" name="search" value="{{ $search }}" 
                       class="form-control form-control-lg rounded-pill border-0 shadow-sm ps-5" 
                       placeholder="Cari judul, penulis, atau kategori..." style="font-size: 0.95rem;">
                <button type="submit" class="btn btn-primary rounded-pill position-absolute top-50 end-0 translate-middle-y me-2 px-4 shadow-sm py-2">
                    Cari
                </button>
            </form>
        </div>

        @if(Auth::user()->Role != 'peminjam')
        <div class="col-lg-5 d-flex justify-content-lg-end gap-2">
            <a href="{{ route('buku.tambah') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center">
                <i class="fa-solid fa-plus me-2"></i> Tambah Buku
            </a>
            <a href="{{ route('kategori.index') }}" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center">
                <i class="fa-solid fa-tags me-2"></i> Tambah Kategori
            </a>
        </div>
        @endif
    </div>

    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Telusuri Kategori</h5>
            <a href="{{ route('dashboard') }}" class="text-decoration-none small fw-bold">Reset Filter</a>
        </div>
        <div class="d-flex gap-2 overflow-auto pb-2 scroll-hide">
            <a href="{{ route('dashboard') }}" 
               class="btn {{ !$kategoriId ? 'btn-primary' : 'btn-outline-secondary border-0 bg-white' }} rounded-pill px-4 shadow-sm fw-bold">
                Semua
            </a>
            @foreach($daftarKategori as $kat)
                <a href="{{ route('dashboard', ['kategori' => $kat->KategoriID]) }}" 
                   class="btn {{ $kategoriId == $kat->KategoriID ? 'btn-primary' : 'btn-outline-secondary border-0 bg-white shadow-sm' }} rounded-pill px-4 fw-bold text-nowrap">
                    {{ $kat->NamaKategori }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="row">
        @forelse($buku as $item)
        <div class="col-6 col-md-4 col-lg-3 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-buku overflow-hidden">
                <div class="position-relative">
                    <img src="{{ $item->Cover ? asset('covers/' . $item->Cover) : 'https://ui-avatars.com/api/?name='.urlencode($item->Judul).'&background=random' }}" 
                         class="card-img-top" alt="{{ $item->Judul }}" style="height: 250px; object-fit: cover;">
                    @if($item->Stok <= 0)
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
                            <span class="badge bg-danger px-3 py-2 rounded-pill">Stok Habis</span>
                        </div>
                    @endif
                </div>
                <div class="card-body p-3">
                    <small class="text-primary fw-bold d-block mb-1">{{ $item->kategori->NamaKategori ?? 'Umum' }}</small>
                    <h6 class="fw-bold text-dark text-truncate mb-1">{{ $item->Judul }}</h6>
                    <p class="text-muted small mb-3 text-truncate">{{ $item->Penulis }}</p>
                    
                    <a href="{{ route('buku.detail', $item->BukuID) }}" class="btn btn-outline-primary w-100 rounded-pill btn-sm fw-bold mb-2">Detail Buku</a>

                    @if(Auth::user()->Role != 'peminjam')
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('buku.edit', $item->BukuID) }}" class="btn btn-warning btn-sm w-100 rounded-pill text-white fw-bold">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                            </a>
                        </div>
                        <div class="col-6">
                            <form action="{{ route('buku.hapus', $item->BukuID) }}" method="POST" onsubmit="return confirm('Hapus buku ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100 rounded-pill fw-bold">
                                    <i class="fa-solid fa-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="100" class="opacity-25 mb-3">
            <p class="text-muted">Ups! Buku yang kamu cari tidak ditemukan.</p>
        </div>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Notifikasi Tugas dari Controller
        @if(isset($notif))
            Swal.fire({
                title: "{{ $notif['title'] }}",
                text: "{{ $notif['text'] }}",
                icon: "{{ $notif['icon'] }}",
                confirmButtonText: "{{ $notif['button'] }}",
                confirmButtonColor: '#0d6efd',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed && "{{ Auth::user()->Role }}" != "peminjam") {
                    window.location.href = "{{ route('laporan') }}";
                }
            });
        @endif

        // Notifikasi Sukses dari Session
        @if(session('success_tambah') || session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success_tambah') ?? session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    });
</script>

<style>
    .scroll-hide::-webkit-scrollbar { display: none; }
    .scroll-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .card-buku { transition: transform 0.3s ease, shadow 0.3s ease; border: 1px solid #f0f0f0; }
    .card-buku:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
    .bg-primary-subtle { background-color: #e7f1ff; }
    .btn-sm { font-size: 0.75rem; }
</style>
@endsection
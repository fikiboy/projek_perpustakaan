@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="header-card p-4 p-md-5 position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 24px;">
                <div class="position-relative z-index-1 text-white">
                    <h1 class="display-5 fw-bold mb-2">Halo, {{ Auth::user()->NamaLengkap }}! ✨</h1>
                    <p class="lead opacity-75">Waktunya menjelajah ilmu. Ada {{ $buku->count() }} buku menantimu hari ini.</p>
                    
                    <div class="d-flex gap-3 mt-4">
                        <div class="stat-box bg-white bg-opacity-20 p-3 rounded-4 backdrop-blur">
                            <small class="d-block opacity-75">Buku Dipinjam</small>
                            <span class="fs-4 fw-bold">{{ $jumlahPinjam }}</span>
                        </div>
                        <div class="stat-box bg-white bg-opacity-20 p-3 rounded-4 backdrop-blur">
                            <small class="d-block opacity-75">Total Koleksi</small>
                            <span class="fs-4 fw-bold text-capitalize">{{ $daftarKategori->count() }} Kategori</span>
                        </div>
                    </div>
                </div>
                <div class="decoration-circle-1"></div>
                <div class="decoration-circle-2"></div>
            </div>
        </div>
    </div>

    <div class="row mb-4 align-items-center g-3">
        <div class="col-lg-6">
            <form action="{{ route('dashboard') }}" method="GET" class="search-form">
                <div class="input-group input-group-lg shadow-sm border-0" style="border-radius: 16px; background: #fff;">
                    <span class="input-group-text bg-transparent border-0 ps-4"><i class="fa-solid fa-magnifying-glass text-primary"></i></span>
                    <input type="text" name="search" class="form-control border-0 py-3" placeholder="Cari judul, penulis, atau genre..." value="{{ $search }}" style="box-shadow: none;">
                    <button class="btn btn-primary px-4 me-2 my-1 rounded-3 fw-bold" type="submit">Cari</button>
                </div>
            </form>
        </div>
        <div class="col-lg-6 text-lg-end d-flex justify-content-lg-end gap-2">
            @if(Auth::user()->Role == 'administrator' || Auth::user()->Role == 'petugas')
                <a href="{{ route('buku.tambah') }}" class="btn btn-dark btn-lg rounded-4 px-4 shadow-sm border-0 d-flex align-items-center">
                    <i class="fa-solid fa-plus-circle me-2"></i> Tambah Buku
                </a>
            @endif
        </div>
    </div>
    
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold m-0 text-dark">Eksplorasi Genre</h5>
        </div>
        <div class="category-scroll d-flex gap-2 overflow-auto pb-2" style="scrollbar-width: none;">
            <a href="{{ route('dashboard') }}" 
               class="btn category-pill {{ !$kategoriId ? 'active' : '' }}">
               Semua Buku
            </a>
            @foreach($daftarKategori as $kat)
                <a href="{{ route('dashboard', ['kategori' => $kat->KategoriID]) }}" 
                   class="btn category-pill {{ $kategoriId == $kat->KategoriID ? 'active' : '' }}">
                   {{ $kat->NamaKategori }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="row g-4">
        @forelse($buku as $item)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 book-card shadow-hover" style="border-radius: 20px; background: #fff;">
                    <div class="position-relative p-2">
                        @if($item->Cover)
                            <img src="{{ asset('covers/' . $item->Cover) }}" class="card-img-top shadow-sm" alt="{{ $item->Judul }}" style="height: 280px; object-fit: cover; border-radius: 16px;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center border-radius-16" style="height: 280px; border-radius: 16px;">
                                <i class="fa-solid fa-book-open fa-3x text-muted opacity-25"></i>
                            </div>
                        @endif
                        
                        <div class="badge-status {{ $item->Stok > 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $item->Stok > 0 ? 'Tersedia' : 'Habis' }}
                        </div>
                    </div>

                    <div class="card-body px-3 pt-2 pb-3">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="text-primary fw-bold small text-uppercase ls-1" style="font-size: 10px;">{{ $item->kategori->NamaKategori ?? 'Umum' }}</span>
                            <div class="rating small text-warning">
                                <i class="fa-solid fa-star"></i> 4.5
                            </div>
                        </div>
                        <h6 class="fw-bold text-dark text-truncate mb-1" title="{{ $item->Judul }}">{{ $item->Judul }}</h6>
                        <p class="text-muted small mb-3">By {{ $item->Penulis }}</p>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('buku.detail', $item->BukuID) }}" class="btn btn-light rounded-3 btn-sm fw-bold py-2 border">
                                Lihat Detail
                            </a>
                            
                            @if(Auth::user()->Role == 'administrator' || Auth::user()->Role == 'petugas')
                                <div class="d-flex gap-2">
                                    <a href="{{ route('buku.edit', $item->BukuID) }}" class="btn btn-outline-warning btn-sm flex-fill rounded-3 border-2">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <form action="{{ route('buku.hapus', $item->BukuID) }}" method="POST" class="flex-fill" onsubmit="return confirm('Hapus buku ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-3 border-2">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="py-5">
                    <i class="fa-solid fa-box-open fa-4x text-muted opacity-25 mb-3"></i>
                    <h5 class="text-muted fw-normal">Yah, buku yang kamu cari tidak ditemukan...</h5>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3 rounded-pill px-4">Tampilkan Semua Buku</a>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    body { background-color: #f8f9fa; }
    
    .backdrop-blur { backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
    
    .decoration-circle-1 {
        position: absolute; width: 300px; height: 300px; background: white; opacity: 0.1;
        border-radius: 50%; top: -100px; right: -50px;
    }
    
    .decoration-circle-2 {
        position: absolute; width: 150px; height: 150px; background: white; opacity: 0.1;
        border-radius: 50%; bottom: -20px; left: 10%;
    }

    .category-pill {
        background: white; border: 1px solid #eee; border-radius: 12px;
        padding: 8px 20px; color: #666; font-weight: 600; white-space: nowrap;
        transition: all 0.3s;
    }
    
    .category-pill:hover, .category-pill.active {
        background: #0d6efd; color: white; border-color: #0d6efd;
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
    }

    .book-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .book-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
    }

    .badge-status {
        position: absolute; top: 15px; left: 15px; padding: 4px 12px;
        border-radius: 8px; font-size: 11px; font-weight: bold; color: white;
        text-transform: uppercase; letter-spacing: 0.5px;
    }

    .ls-1 { letter-spacing: 1px; }
    .category-scroll::-webkit-scrollbar { display: none; }
</style>
@endsection
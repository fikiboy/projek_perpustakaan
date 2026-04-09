@extends('layouts.app')

@section('content')
<div class="container mt-4 animate__animated animate__fadeIn">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5" style="background: linear-gradient(to right, #4e73df, #224abe);">
        <div class="card-body p-5 text-white">
            <h2 class="fw-bold mb-2">Rak Buku Pribadi 📚</h2>
            <p class="opacity-75 mb-0">Kelola buku yang sedang kamu pinjam dan tandai buku impianmu.</p>
        </div>
    </div>

    <ul class="nav nav-pills mb-4 bg-light p-2 rounded-pill d-inline-flex shadow-sm" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 fw-bold" id="pills-pinjam-tab" data-bs-toggle="pill" data-bs-target="#pills-pinjam" type="button" role="tab" aria-controls="pills-pinjam" aria-selected="true">
                <i class="fa-solid fa-book-open me-2"></i>Pinjaman Saya
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold" id="pills-favorit-tab" data-bs-toggle="pill" data-bs-target="#pills-favorit" type="button" role="tab" aria-controls="pills-favorit" aria-selected="false">
                <i class="fa-solid fa-heart me-2 text-danger"></i>Favorit
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-pinjam" role="tabpanel" aria-labelledby="pills-pinjam-tab">
            <div class="row">
                @forelse($koleksi as $item)
                @php 
                    $buku = $item->buku; 
                    $ulasan = $buku->ulasanUser; 
                @endphp
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden card-koleksi">
                        <div class="d-flex h-100">
                            <div style="width: 40%;">
                                <img src="{{ $buku->Cover ? asset('covers/' . $buku->Cover) : 'https://ui-avatars.com/api/?name='.urlencode($buku->Judul) }}" 
                                     class="h-100 w-100" style="object-fit: cover;">
                            </div>

                            <div class="p-3 d-flex flex-column justify-content-between" style="width: 60%;">
                                <div>
                                    <div class="mb-2">
                                        @php
                                            $badgeClass = 'bg-primary';
                                            if($item->StatusPeminjaman == 'Menunggu Pengecekan') $badgeClass = 'bg-warning text-dark';
                                            elseif($item->StatusPeminjaman == 'Dikembalikan') $badgeClass = 'bg-success';
                                            elseif($item->StatusPeminjaman == 'Ditolak') $badgeClass = 'bg-danger';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} rounded-pill small px-3">
                                            {{ $item->StatusPeminjaman }}
                                        </span>
                                    </div>
                                    <h6 class="fw-bold text-dark text-truncate mb-0">{{ $buku->Judul }}</h6>
                                    <small class="text-muted d-block mb-2">By {{ $buku->Penulis }}</small>

                                    @if($ulasan)
                                        <div class="text-warning mb-2" style="font-size: 10px;">
                                            @for($i=1; $i<=5; $i++)
                                                <i class="fa-{{ $i <= $ulasan->Rating ? 'solid' : 'regular' }} fa-star"></i>
                                            @endfor
                                            <span class="text-muted ms-1">({{ $ulasan->Rating }}/5)</span>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between border-top pt-2 mb-3">
                                        <div>
                                            <p class="text-muted mb-0" style="font-size: 9px; text-uppercase;">Pinjam</p>
                                            <p class="fw-bold mb-0" style="font-size: 11px;">{{ \Carbon\Carbon::parse($item->TanggalPeminjaman)->format('d M Y') }}</p>
                                        </div>
                                        @if($item->StatusPeminjaman == 'Dipinjam')
                                        <div>
                                            <p class="text-danger mb-0" style="font-size: 9px; text-uppercase;">Batas</p>
                                            <p class="fw-bold text-danger mb-0" style="font-size: 11px;">{{ \Carbon\Carbon::parse($item->TanggalPeminjaman)->addDays(7)->format('d M') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    {{-- TOMBOL ULASAN --}}
                                    @if(!$ulasan)
                                        <a href="{{ route('buku.detail', $buku->BukuID) }}#form-ulasan" class="btn btn-warning btn-sm fw-bold rounded-pill text-white" style="font-size: 11px;">
                                            <i class="fa-solid fa-star me-1"></i> Beri Ulasan
                                        </a>
                                    @else
                                        <a href="{{ route('buku.detail', $buku->BukuID) }}#form-ulasan" class="btn btn-outline-warning btn-sm fw-bold rounded-pill" style="font-size: 11px;">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit Ulasan
                                        </a>
                                    @endif

                                    {{-- LOGIKA TOMBOL STATUS DITANGANI DISINI --}}
                                    @if($item->StatusPeminjaman == 'Dipinjam')
                                        <a href="{{ route('peminjaman.bukti', $item->PeminjamanID) }}" class="btn btn-light btn-sm border fw-bold rounded-pill" style="font-size: 11px;">
                                            <i class="fa-solid fa-print me-1"></i> Bukti Pinjam
                                        </a>

                                        <form action="{{ route('buku.kembalikan', $item->PeminjamanID) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold rounded-pill" style="font-size: 11px;">
                                                <i class="fa-solid fa-rotate-left me-1"></i> Kembalikan Buku
                                            </button>
                                        </form>
                                    @elseif($item->StatusPeminjaman == 'Menunggu Pengecekan')
                                        <button class="btn btn-warning btn-sm w-100 fw-bold rounded-pill text-white shadow-sm" style="font-size: 11px;" disabled>
                                            <i class="fa-solid fa-hourglass-half me-1"></i> Menunggu Pengecekan
                                        </button>
                                    @elseif($item->StatusPeminjaman == 'Dikembalikan')
                                        <a href="{{ route('peminjaman.bukti', $item->PeminjamanID) }}" class="btn btn-success btn-sm fw-bold rounded-pill shadow-sm text-white" style="font-size: 11px;">
                                            <i class="fa-solid fa-file-invoice me-1"></i> Bukti Pengembalian
                                        </a>
                                        <div class="alert alert-success py-1 px-2 rounded-pill text-center mb-0" style="font-size: 10px;">
                                            <i class="fa-solid fa-check-circle"></i> Selesai Dikembalikan
                                        </div>
                                    @elseif($item->StatusPeminjaman == 'Ditolak')
                                        <div class="alert alert-danger py-1 px-2 rounded-pill text-center mb-0" style="font-size: 10px;">
                                            <i class="fa-solid fa-circle-xmark"></i> Pengembalian Ditolak
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <img src="https://illustrations.popsy.co/blue/reading-a-book.svg" style="height: 150px;" class="mb-3">
                    <p class="text-muted">Belum ada buku yang kamu pinjam.</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="tab-pane fade" id="pills-favorit" role="tabpanel" aria-labelledby="pills-favorit-tab">
            <div class="row">
                @forelse($favorit as $fav)
                @php $bF = $fav->buku; @endphp
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 card-koleksi">
                        <img src="{{ $bF->Cover ? asset('covers/' . $bF->Cover) : 'https://ui-avatars.com/api/?name='.urlencode($bF->Judul) }}" 
                             class="card-img-top" style="height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="fw-bold text-dark text-truncate mb-1">{{ $bF->Judul }}</h6>
                            <p class="text-primary small mb-3">{{ $bF->Penulis }}</p>
                            
                            <div class="d-grid gap-2">
                                <a href="{{ route('buku.detail', $bF->BukuID) }}" class="btn btn-primary btn-sm rounded-pill fw-bold">
                                    Lihat Detail
                                </a>
                                <form action="{{ route('buku.favorit', $bF->BukuID) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill w-100">
                                        <i class="fa-solid fa-heart-crack me-1"></i> Hapus Favorit
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <img src="https://illustrations.popsy.co/blue/studying.svg" style="height: 150px;" class="mb-3">
                    <p class="text-muted">Belum ada buku favorit. Klik ikon hati di detail buku untuk menambahkan!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .card-koleksi { transition: 0.3s; border: 1px solid #eee; }
    .card-koleksi:hover { transform: translateY(-5px); box-shadow: 0 12px 20px rgba(0,0,0,0.1) !important; }
    .nav-pills .nav-link.active { background-color: #4e73df; }
    .nav-pills .nav-link { color: #5a5c69; margin-right: 5px; }
    .border-dashed { border: 2px dashed #dee2e6; }
</style>
@endsection
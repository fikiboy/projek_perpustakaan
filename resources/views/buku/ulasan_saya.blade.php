@extends('layouts.app')

@section('content')
<div class="container mt-3 animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Ulasan Saya 💬</h4>
            <p class="text-muted small">Riwayat penilaian buku yang telah Anda baca.</p>
        </div>
        <div class="bg-warning bg-opacity-10 p-2 rounded-3">
            <i class="fa-solid fa-star text-warning fs-4"></i>
        </div>
    </div>

    <div class="row g-3">
        @forelse($ulasan as $u)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden shadow-hover">
                <div class="card-body p-3">
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            @if($u->buku->Cover)
                                <img src="{{ asset('covers/' . $u->buku->Cover) }}" class="rounded-3 shadow-sm" style="width: 50px; height: 75px; object-fit: cover;">
                            @else
                                <div class="rounded-3 bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 75px; font-size: 10px;">No Cover</div>
                            @endif
                        </div>
                        
                        <div class="flex-grow-1 ms-3">
                            <h6 class="fw-bold mb-1 text-truncate" style="max-width: 200px;">{{ $u->buku->Judul }}</h6>
                            <div class="text-warning mb-1" style="font-size: 12px;">
                                @for($i=1; $i<=5; $i++)
                                    <i class="fa-{{ $i <= $u->Rating ? 'solid' : 'regular' }} fa-star"></i>
                                @endfor
                            </div>
                            <small class="text-muted" style="font-size: 11px;">
                                <i class="fa-regular fa-clock me-1"></i> {{ $u->created_at ? $u->created_at->diffForHumans() : 'Baru saja' }}
                            </small>
                        </div>
                    </div>
                    
                    <div class="bg-light p-2 rounded-3">
                        <p class="mb-0 text-dark small italic" style="font-style: italic;">
                            <i class="fa-solid fa-quote-left me-1 text-muted opacity-50"></i>
                            {{ $u->Ulasan }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="opacity-25 mb-3">
                <i class="fa-solid fa-comment-slash" style="font-size: 60px;"></i>
            </div>
            <h6 class="fw-bold text-muted">Belum ada ulasan yang dibuat</h6>
            <a href="/koleksi" class="btn btn-sm btn-outline-primary rounded-pill mt-2">Beri Ulasan Sekarang</a>
        </div>
        @endforelse
    </div>
</div>

<style>
    .shadow-hover { transition: 0.3s; }
    .shadow-hover:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.08) !important; }
</style>
@endsection
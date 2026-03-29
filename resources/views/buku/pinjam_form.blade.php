@extends('layouts.app')

@section('content')
<div class="container mt-4" style="max-width: 500px;">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden animate-up">
        <div class="p-4 text-center bg-primary text-white">
            <h4 class="fw-bold mb-0">Konfirmasi Peminjaman</h4>
        </div>
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <img src="{{ $buku->Cover ? asset('covers/' . $buku->Cover) : 'https://ui-avatars.com/api/?name='.urlencode($buku->Judul) }}" 
                     class="rounded-3 shadow-sm mb-3" style="height: 120px;">
                <h6 class="fw-bold mb-0">{{ $buku->Judul }}</h6>
                <small class="text-muted">{{ $buku->Penulis }}</small>
            </div>

            <form action="{{ route('buku.pinjam.store', $buku->BukuID) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Tanggal Pinjam</label>
                    <input type="text" class="form-control border-0 bg-light rounded-3" value="{{ date('d F Y') }}" readonly>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">Tanggal Pengembalian</label>
                    <input type="date" name="TanggalPengembalian" class="form-control rounded-3" 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    <small class="text-info mt-1 d-block" style="font-size: 11px;">*Maksimal peminjaman adalah 7 hari.</small>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2 rounded-pill fw-bold shadow">Konfirmasi Pinjam</button>
                    <a href="{{ route('buku.detail', $buku->BukuID) }}" class="btn btn-light py-2 rounded-pill fw-bold text-muted">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
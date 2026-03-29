@extends('layouts.app')

@section('content')
<div class="container mt-4 animate__animated animate__fadeIn">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Laporan Peminjaman Buku</h5>
            <button onclick="window.print()" class="btn btn-success btn-sm rounded-pill px-3">
                <i class="fa-solid fa-print me-2"></i> Cetak Laporan (PDF)
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Peminjam</th>
                            <th>Judul Buku</th>
                            <th>Tgl Pinjam</th>
                            <th class="text-center">Status</th>
                            <th class="text-center aksi-column">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporan as $key => $l)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $l->user->NamaLengkap }}</div>
                                <small class="text-muted">{{ $l->user->Email }}</small>
                            </td>
                            <td>{{ $l->buku->Judul }}</td>
                            <td>{{ \Carbon\Carbon::parse($l->TanggalPeminjaman)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @php
                                    $badgeClass = 'bg-secondary';
                                    if($l->StatusPeminjaman == 'Menunggu') $badgeClass = 'bg-warning text-dark';
                                    elseif($l->StatusPeminjaman == 'Dipinjam') $badgeClass = 'bg-primary';
                                    elseif($l->StatusPeminjaman == 'Dikembalikan') $badgeClass = 'bg-success';
                                    elseif($l->StatusPeminjaman == 'Ditolak') $badgeClass = 'bg-danger';
                                @endphp
                                <span class="badge {{ $badgeClass }} rounded-pill px-3">
                                    {{ $l->StatusPeminjaman }}
                                </span>
                            </td>
                            <td class="text-center aksi-column">
                                @if($l->StatusPeminjaman == 'Menunggu')
                                    <div class="d-flex justify-content-center gap-2">
                                        <form action="{{ route('peminjaman.approve', $l->PeminjamanID) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">Setujui</button>
                                        </form>
                                        <form action="{{ route('peminjaman.tolak', $l->PeminjamanID) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">Tolak</button>
                                        </form>
                                    </div>
                                @else
                                    <small class="text-muted italic">-</small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS Khusus agar saat di-print, elemen navigasi hilang */
    @media print {
        .navbar-top, .bottom-nav, .btn, .aksi-column, .card-header h5::after {
            display: none !important;
        }
        .container { width: 100% !important; max-width: 100% !important; margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        body { background: white !important; }
        .table td, .table th { padding: 10px !important; }
    }
</style>
@endsection
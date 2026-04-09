@extends('layouts.app')

@section('content')
<div class="container mt-4 animate__animated animate__fadeIn">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center d-print-none">
            <h5 class="fw-bold mb-0">Laporan Peminjaman & Pengembalian</h5>
            <button onclick="window.print()" class="btn btn-success btn-sm rounded-pill px-3">
                <i class="fa-solid fa-print me-2"></i> Cetak Laporan (PDF)
            </button>
        </div>

        <div class="card-body p-4">
            <div class="kop-surat d-none d-print-block text-center mb-4">
                <h4 class="fw-bold mb-1">PERPUSTAKAAN DIGITAL IBOOKU</h4>
                <p class="mb-0 small">Kecamatan Gunungputri, Kabupaten Bogor, Jawa Barat</p>
                <p class="small italic">Laporan Data Peminjaman & Pengembalian Buku</p>
                <hr style="border: 2px solid black;">
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle border-print">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Peminjam</th>
                            <th>Judul Buku</th>
                            <th class="text-center">Tgl Pinjam</th>
                            <th class="text-center">Status</th>
                            <th class="text-center aksi-column">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporan as $key => $l)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $l->user->NamaLengkap }}</div>
                                <small class="text-muted">{{ $l->user->Email }}</small>
                            </td>
                            <td>{{ $l->buku->Judul }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($l->TanggalPeminjaman)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @php
                                    $badgeClass = 'bg-secondary';
                                    if($l->StatusPeminjaman == 'Menunggu') $badgeClass = 'bg-warning text-dark';
                                    elseif($l->StatusPeminjaman == 'Dipinjam') $badgeClass = 'bg-primary';
                                    elseif($l->StatusPeminjaman == 'Menunggu Pengecekan') $badgeClass = 'bg-info text-white';
                                    elseif($l->StatusPeminjaman == 'Dikembalikan') $badgeClass = 'bg-success';
                                    elseif($l->StatusPeminjaman == 'Ditolak') $badgeClass = 'bg-danger';
                                @endphp
                                <span class="badge {{ $badgeClass }} rounded-pill px-3">
                                    {{ $l->StatusPeminjaman }}
                                </span>
                            </td>
                            <td class="text-center aksi-column">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- AKSI APPROVAL PEMINJAMAN AWAL --}}
                                    @if($l->StatusPeminjaman == 'Menunggu')
                                        <form action="{{ route('peminjaman.approve', $l->PeminjamanID) }}" method="POST">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">Setujui Pinjam</button>
                                        </form>
                                        <form action="{{ route('peminjaman.tolak', $l->PeminjamanID) }}" method="POST">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">Tolak</button>
                                        </form>

                                    {{-- AKSI APPROVAL PENGEMBALIAN --}}
                                    @elseif($l->StatusPeminjaman == 'Menunggu Pengecekan')
                                        <form action="{{ route('peminjaman.setujui_kembali', $l->PeminjamanID) }}" method="POST">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Setujui Kembali</button>
                                        </form>
                                        <button type="button" class="btn btn-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTolakKembali{{ $l->PeminjamanID }}">
                                            Tolak
                                        </button>

                                    {{-- FITUR HAPUS LAPORAN (Untuk status yang sudah selesai/ditolak) --}}
                                    @else
                                        <form action="{{ route('peminjaman.destroy', $l->PeminjamanID) }}" method="POST" onsubmit="return confirm('Hapus data laporan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-light btn-sm rounded-pill text-danger">
                                                <i class="fa-solid fa-trash-can"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="modal fade" id="modalTolakKembali{{ $l->PeminjamanID }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <form action="{{ route('peminjaman.tolak_kembali', $l->PeminjamanID) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-body p-4 text-center">
                                                    <i class="fa-solid fa-user-slash text-danger fs-1 mb-3"></i>
                                                    <h6 class="fw-bold">Tangguhkan Akun?</h6>
                                                    <textarea name="alasan" class="form-control bg-light border-0 small" rows="3" placeholder="Alasan penangguhan..." required></textarea>
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0">
                                                    {{-- Perbaikan: Menambahkan data-bs-dismiss="modal" untuk mencegah layar menghitam --}}
                                                    <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold" data-bs-dismiss="modal">
                                                        Tolak & Suspend
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-none d-print-block mt-5">
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4 text-center">
                        <p class="mb-1">Gunungputri, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                        <p class="mb-5">
                            @if(Auth::user()->Role == 'administrator')
                                Administrator,
                            @else
                                Petugas Perpustakaan,
                            @endif
                        </p>
                        <br>
                        <h6 class="fw-bold mb-0 text-decoration-underline">{{ Auth::user()->NamaLengkap }}</h6>
                        <small>NIP. __________________________</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-print { border: 1px solid #dee2e6; }
    @media print {
        @page { size: A4; margin: 2cm; }
        .navbar-top, .bottom-nav, .btn, .aksi-column, .d-print-none, .card-header { display: none !important; }
        .container { width: 100% !important; max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        body { background: white !important; font-family: "Times New Roman", Times, serif !important; }
        .table { width: 100% !important; border-collapse: collapse !important; }
        .table th, .table td { border: 1px solid black !important; padding: 8px !important; font-size: 12px !important; }
        .table thead th { background-color: #f2f2f2 !important; -webkit-print-color-adjust: exact; }
        .d-print-block { display: block !important; }
        .row { display: flex !important; flex-wrap: nowrap !important; }
        .col-8 { width: 66.6% !important; }
        .col-4 { width: 33.3% !important; }
    }

    /* Menghilangkan paksa efek hitam jika modal error/nyangkut */
    .modal-open {
        overflow: auto !important;
        padding-right: 0 !important;
    }
    /* Memaksa backdrop hilang saat navigasi halaman */
    .modal-backdrop {
        display: none !important;
    }
    .modal.show ~ .modal-backdrop {
        display: block !important;
    }
</style>
@endsection
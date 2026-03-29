<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Peminjaman - {{ $pinjam->buku->Judul }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .ticket-card {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            border: none;
        }
        .header-gradient {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            padding: 40px;
            color: white;
            text-align: center;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(5px);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }
        .info-section { padding: 40px; }
        .label-custom { color: #858796; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
        .value-custom { color: #2d3436; font-weight: 600; font-size: 16px; margin-bottom: 20px; }
        .divider { border-top: 2px dashed #e3e6f0; margin: 30px 0; position: relative; }
        .divider::before, .divider::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background: #f8f9fa;
            border-radius: 50%;
            top: -11px;
        }
        .divider::before { left: -50px; }
        .divider::after { right: -50px; }
        
        @media print {
            body { background: white; }
            .ticket-card { box-shadow: none; margin: 0; width: 100%; max-width: 100%; border: 1px solid #eee; }
            .no-print { display: none; }
            .header-gradient { background: #4e73df !important; -webkit-print-color-adjust: exact; }
            .divider::before, .divider::after { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container no-print mt-4 text-center">
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 me-2">
            <i class="fa-solid fa-print me-2"></i> Cetak Ulang
        </button>
        <a href="{{ route('koleksi') }}" class="btn btn-light rounded-pill px-4 border">
            <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Koleksi
        </a>
    </div>

    <div class="ticket-card">
        <div class="header-gradient">
            <div class="mb-3">
                <i class="fa-solid fa-book-bookmark fa-3x"></i>
            </div>
            <div class="status-badge mb-3">{{ $pinjam->StatusPeminjaman }}</div>
            <h2 class="fw-bold mb-0">BUKTI PEMINJAMAN</h2>
            <p class="opacity-75 small">Perpustakaan Digital - ID Transaksi #{{ str_pad($pinjam->PeminjamanID, 5, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="info-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <p class="label-custom mb-1">Nama Peminjam</p>
                        <p class="value-custom">{{ $pinjam->user->NamaLengkap }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="label-custom mb-1">Email / Username</p>
                        <p class="value-custom">{{ $pinjam->user->Email }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4 text-md-end">
                        <p class="label-custom mb-1">Tanggal Pinjam</p>
                        <p class="value-custom">{{ \Carbon\Carbon::parse($pinjam->TanggalPeminjaman)->format('d F Y') }}</p>
                    </div>
                    <div class="mb-4 text-md-end">
                        <p class="label-custom mb-1">Batas Pengembalian</p>
                        <p class="value-custom text-danger">{{ \Carbon\Carbon::parse($pinjam->TanggalPeminjaman)->addDays(7)->format('d F Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="row align-items-center">
                <div class="col-3 col-md-2">
                    <img src="{{ $pinjam->buku->Cover ? asset('covers/' . $pinjam->buku->Cover) : 'https://ui-avatars.com/api/?name='.urlencode($pinjam->buku->Judul) }}" 
                         class="img-fluid rounded-3 shadow-sm">
                </div>
                <div class="col-9 col-md-10">
                    <p class="label-custom mb-1">Buku yang Dipinjam</p>
                    <h4 class="fw-bold text-dark mb-1">{{ $pinjam->buku->Judul }}</h4>
                    <p class="text-primary mb-0 fw-medium small">Karya: {{ $pinjam->buku->Penulis }}</p>
                    <p class="text-muted small mb-0">Penerbit: {{ $pinjam->buku->Penerbit }} ({{ $pinjam->buku->TahunTerbit }})</p>
                </div>
            </div>

            <div class="mt-5 p-3 bg-light rounded-4 text-center border">
                <p class="small text-muted mb-0">
                    <i class="fa-solid fa-circle-info me-2 text-primary"></i>
                    Harap kembalikan buku tepat waktu untuk menghindari denda atau sanksi akun. 
                    Tunjukkan bukti digital ini jika diminta oleh petugas perpustakaan.
                </p>
            </div>
        </div>

        <div class="bg-light p-4 text-center border-top">
            <div class="d-flex justify-content-center gap-4 opacity-50 mb-2">
                <i class="fa-brands fa-instagram"></i>
                <i class="fa-brands fa-facebook"></i>
                <i class="fa-brands fa-twitter"></i>
            </div>
            <p class="small text-muted mb-0">Dicetak secara otomatis pada {{ now()->format('d/m/Y H:i') }} WIB</p>
        </div>
    </div>

</body>
</html>
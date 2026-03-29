@extends('layouts.app')

@section('content')
<div class="container mt-4 animate__animated animate__fadeIn">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Manajemen Kategori</h4>
            <p class="text-muted small">Kelola kategori buku seperti Sains, Novel, atau Edukasi</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold btn-sm shadow-sm">
            <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold mb-3"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Buat Kategori Baru</h6>
                <form action="{{ route('kategori.simpan') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Kategori</label>
                        <input type="text" name="NamaKategori" 
                               class="form-control rounded-pill bg-light border-0 py-2 px-3" 
                               placeholder="Contoh: Komik, Biografi..." required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm py-2">
                        <i class="fa-solid fa-save me-2"></i> Simpan Kategori
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold"><i class="fa-solid fa-list text-primary me-2"></i>Daftar Kategori Saat Ini</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 border-0 small text-uppercase text-muted" style="width: 80px;">No</th>
                                    <th class="py-3 border-0 small text-uppercase text-muted">Nama Kategori</th>
                                    <th class="py-3 border-0 small text-uppercase text-muted text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kategori as $k)
                                <tr>
                                    <td class="ps-4 align-middle text-muted small">{{ $loop->iteration }}</td>
                                    <td class="align-middle">
                                        <span class="fw-bold text-dark">{{ $k->NamaKategori }}</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <form action="{{ route('kategori.hapus', $k->KategoriID) }}" method="POST" id="delete-form-{{ $k->KategoriID }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" 
                                                    onclick="confirmDelete('{{ $k->KategoriID }}', '{{ $k->NamaKategori }}')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted italic">
                                        <i class="fa-solid fa-folder-open d-block fs-2 mb-2 opacity-25"></i>
                                        Belum ada kategori yang ditambahkan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert & Script Konfirmasi --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Kategori?',
            text: "Kategori '" + name + "' akan dihapus. Buku dengan kategori ini mungkin kehilangan relasinya.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            borderRadius: '15px'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 1500,
        borderRadius: '15px'
    });
</script>
@endif

<style>
    .form-control:focus {
        background-color: #fff !important;
        border: 1px solid #0d6efd !important;
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.1) !important;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9ff;
    }
</style>
@endsection
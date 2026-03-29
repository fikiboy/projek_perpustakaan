@extends('layouts.app')

@section('content')
<div class="container mt-4 animate__animated animate__fadeIn">
    <div class="mb-5">
        <h4 class="fw-bold text-primary mb-1"><i class="fa-solid fa-users-gear me-2"></i>Manajemen Pengguna</h4>
        <p class="text-muted small">Daftar seluruh pengguna sistem perpustakaan iBooku</p>
    </div>
    
    @if(Auth::user()->Role == 'administrator')
    <button type="button" class="btn btn-dark rounded-pill mb-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPetugas">
        <i class="fa-solid fa-user-plus me-2"></i> Tambah Petugas Baru
    </button>
    @endif

    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <i class="fa-solid fa-user-gear text-danger fs-5 me-2"></i>
            <h5 class="fw-bold mb-0">Administrator</h5>
        </div>
        <div class="table-responsive table-container">
            @include('auth.partials.table_user', [
                'data' => $users->where('Role', 'administrator'), 
                'color' => 'danger'
            ])
        </div>
    </div>

    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <i class="fa-solid fa-user-shield text-primary fs-5 me-2"></i>
            <h5 class="fw-bold mb-0">Petugas</h5>
        </div>
        <div class="table-responsive table-container">
            @include('auth.partials.table_user', [
                'data' => $users->where('Role', 'petugas'), 
                'color' => 'primary'
            ])
        </div>
    </div>

    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <i class="fa-solid fa-users text-secondary fs-5 me-2"></i>
            <h5 class="fw-bold mb-0">Peminjam</h5>
        </div>
        <div class="table-responsive table-container">
            @include('auth.partials.table_user', [
                'data' => $users->where('Role', 'peminjam'), 
                'color' => 'secondary'
            ])
        </div>
    </div>
</div>

@if(Auth::user()->Role == 'administrator')
<div class="modal fade" id="modalPetugas" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">Daftarkan Petugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.tambah_petugas') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username</label>
                        <input type="text" name="Username" class="form-control rounded-pill bg-light border-0 py-2 px-3" placeholder="Username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="Email" class="form-control rounded-pill bg-light border-0 py-2 px-3" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="Password" class="form-control rounded-pill bg-light border-0 py-2 px-3" placeholder="Password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="NamaLengkap" class="form-control rounded-pill bg-light border-0 py-2 px-3" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat</label>
                        <textarea name="Alamat" class="form-control rounded-4 bg-light border-0 px-3" rows="3" placeholder="Alamat Lengkap" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">Simpan Akun Petugas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@foreach($users as $u)
    @if($u->UserID != Auth::id())
    <div class="modal fade" id="modalEditRole{{ $u->UserID }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold">Ubah Role User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('user.update_role', $u->UserID) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">Pilih akses untuk <strong>{{ $u->NamaLengkap }}</strong>:</p>
                        
                        <div class="d-grid gap-2">
                            <input type="radio" class="btn-check" name="Role" id="role1_{{ $u->UserID }}" value="peminjam" {{ $u->Role == 'peminjam' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary rounded-pill text-start px-3 py-2" for="role1_{{ $u->UserID }}">
                                <i class="fa-solid fa-users me-2"></i> Peminjam
                            </label>

                            <input type="radio" class="btn-check" name="Role" id="role2_{{ $u->UserID }}" value="petugas" {{ $u->Role == 'petugas' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary rounded-pill text-start px-3 py-2" for="role2_{{ $u->UserID }}">
                                <i class="fa-solid fa-user-shield me-2"></i> Petugas
                            </label>

                            @if(Auth::user()->Role == 'administrator')
                            <input type="radio" class="btn-check" name="Role" id="role3_{{ $u->UserID }}" value="administrator" {{ $u->Role == 'administrator' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger rounded-pill text-start px-3 py-2" for="role3_{{ $u->UserID }}">
                                <i class="fa-solid fa-user-gear me-2"></i> Administrator
                            </label>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

<style>
    .table-container { 
        background: white; 
        border-radius: 15px; 
        overflow: hidden; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #f1f1f1;
    }
    .form-control:focus {
        background-color: #fff !important;
        border: 1px solid #0d6efd !important;
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.1) !important;
    }
    .modal-backdrop { z-index: 1040 !important; }
    .modal { z-index: 1050 !important; }
    
    /* Style untuk radio button yang tampak seperti tombol biasa */
    .btn-check:checked + .btn-outline-primary { background-color: #0d6efd; color: white; }
    .btn-check:checked + .btn-outline-danger { background-color: #dc3545; color: white; }
    .btn-check:checked + .btn-outline-secondary { background-color: #6c757d; color: white; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDeleteUser(userId, userName) {
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Akun " + userName + " akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        borderRadius: '15px'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + userId).submit();
        }
    })
}

@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 2000
    });
@endif
</script>
@endsection
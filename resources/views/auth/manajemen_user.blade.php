@extends('layouts.app')

@section('content')
<div class="container mt-4 animate__animated animate__fadeIn">
    <div class="mb-5">
        <h4 class="fw-bold text-primary mb-1"><i class="fa-solid fa-users-gear me-2"></i>Manajemen Pengguna</h4>
        <p class="text-muted small">Kelola pengguna dan persetujuan akun pendaftar baru</p>
    </div>
    
    @if(Auth::user()->Role == 'administrator')
    <button type="button" class="btn btn-dark rounded-pill mb-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPetugas">
        <i class="fa-solid fa-user-plus me-2"></i> Tambah Petugas Baru
    </button>
    @endif

    {{-- Kategori Administrator --}}
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

    {{-- Kategori Petugas --}}
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

    {{-- Kategori Peminjam --}}
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
{{-- Modal Tambah Petugas --}}
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
    <input type="text" name="Alamat" list="desa-petugas" class="form-control rounded-pill bg-light border-0 py-2 px-3" placeholder="Pilih atau Ketik Alamat..." required>
    <datalist id="desa-petugas">
        <option value="Gunung Putri">
        <option value="Wanaherang">
        <option value="Tlajung Udik">
        <option value="Bojong Kulur">
        <option value="Bojong Nangka">
        <option value="Cicadas">
        <option value="Cikeas Udik">
        <option value="Ciangsana">
        <option value="Nagrak">
        <option value="Karanggan">
    </datalist>
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

{{-- Modal Edit Role --}}
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
                        <div class="d-grid gap-2">
                            <input type="radio" class="btn-check" name="Role" id="role1_{{ $u->UserID }}" value="peminjam" {{ $u->Role == 'peminjam' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary rounded-pill text-start px-3 py-2" for="role1_{{ $u->UserID }}">Peminjam</label>

                            <input type="radio" class="btn-check" name="Role" id="role2_{{ $u->UserID }}" value="petugas" {{ $u->Role == 'petugas' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary rounded-pill text-start px-3 py-2" for="role2_{{ $u->UserID }}">Petugas</label>

                            @if(Auth::user()->Role == 'administrator')
                            <input type="radio" class="btn-check" name="Role" id="role3_{{ $u->UserID }}" value="administrator" {{ $u->Role == 'administrator' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger rounded-pill text-start px-3 py-2" for="role3_{{ $u->UserID }}">Administrator</label>
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
    .table-container { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #f1f1f1; }
    .btn-check:checked + .btn-outline-primary { background-color: #0d6efd; color: white; }
    .btn-check:checked + .btn-outline-danger { background-color: #dc3545; color: white; }
    .btn-check:checked + .btn-outline-secondary { background-color: #6c757d; color: white; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDeleteUser(userId, userName) {
    Swal.fire({
        title: 'Hapus Akun?',
        text: "Akun " + userName + " akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!'
    }).then((result) => {
        if (result.isConfirmed) document.getElementById('delete-form-' + userId).submit();
    })
}
@if(session('success'))
    Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", showConfirmButton: false, timer: 2000 });
@endif
</script>
@endsection
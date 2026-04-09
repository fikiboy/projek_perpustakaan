<div class="table-responsive table-container border">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th width="25%">Nama Lengkap</th>
                <th width="25%">Email / Username</th>
                <th width="20%">Status</th>
                <th width="15%">Alamat</th>
                <th width="15%" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $u)
            <tr>
                <td class="ps-4">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($u->NamaLengkap) }}&background=random&color=fff" 
                             class="rounded-circle me-3" width="35" height="35">
                        <span class="fw-bold text-dark">{{ $u->NamaLengkap }}</span>
                    </div>
                </td>
                <td>
                    <div class="small fw-bold">{{ $u->Email }}</div>
                    <div class="small text-muted">@ {{ $u->Username ?? 'user' }}</div>
                </td>
                <td>
                    {{-- Badge Status --}}
                    @if($u->Status == 'Aktif')
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 small">Aktif</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 small">Pending</span>
                    @endif
                </td>
                <td class="small text-muted">
                    {{ Str::limit($u->Alamat, 30) ?: '-' }}
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        {{-- Tombol Ganti Status (Approve/Suspend) --}}
                        <form action="{{ route('user.toggle_status', $u->UserID) }}" method="POST" class="m-0">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-link p-0 {{ $u->Status == 'Aktif' ? 'text-warning' : 'text-success' }}" 
                                    title="{{ $u->Status == 'Aktif' ? 'Suspend Akun' : 'Setujui Akun' }}">
                                @if($u->Status == 'Aktif')
                                    <i class="fa-solid fa-circle-pause fs-5"></i>
                                @else
                                    <i class="fa-solid fa-circle-check fs-5"></i>
                                @endif
                            </button>
                        </form>

                        @if($u->UserID != Auth::id())
                            {{-- Tombol Edit Role --}}
                            <button type="button" 
                                    class="btn btn-link text-primary p-0" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditRole{{ $u->UserID }}"
                                    title="Ubah Role">
                                <i class="fa-solid fa-user-tag fs-5"></i>
                            </button>

                            {{-- Tombol Hapus --}}
                            <button type="button" 
                                    onclick="confirmDeleteUser('{{ $u->UserID }}', '{{ addslashes($u->NamaLengkap) }}')" 
                                    class="btn btn-link text-danger p-0"
                                    title="Hapus Pengguna">
                                <i class="fa-solid fa-trash-can fs-5"></i>
                            </button>

                            <form id="delete-form-{{ $u->UserID }}" action="{{ route('user.hapus', $u->UserID) }}" method="POST" style="display: none;">
                                @csrf @method('DELETE')
                            </form>
                        @else
                            <span class="badge bg-light text-muted border small px-2 py-1">Saya</span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-4 text-muted small italic">
                    Belum ada data di bagian ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
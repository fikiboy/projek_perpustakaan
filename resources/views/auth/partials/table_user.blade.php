<div class="table-responsive table-container border">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th width="30%">Nama Lengkap</th>
                <th width="30%">Email / Username</th>
                <th width="30%">Alamat</th>
                <th width="15%" class="text-center">Aksi</th> </tr>
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
                <td class="small text-muted">
                    {{ Str::limit($u->Alamat, 50) ?: '-' }}
                </td>
                <td class="text-center">
                    @if($u->UserID != Auth::id())
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" 
                                class="btn btn-link text-primary p-0" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEditRole{{ $u->UserID }}"
                                title="Ubah Role">
                            <i class="fa-solid fa-user-tag"></i>
                        </button>

                        <button type="button" 
                                onclick="confirmDeleteUser('{{ $u->UserID }}', '{{ addslashes($u->NamaLengkap) }}')" 
                                class="btn btn-link text-danger p-0"
                                title="Hapus Pengguna">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>

                        <form id="delete-form-{{ $u->UserID }}" action="{{ route('user.hapus', $u->UserID) }}" method="POST" style="display: none;">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                    @else
                    <span class="badge bg-light text-muted border small px-2 py-1">Saya</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-4 text-muted small italic">
                    Belum ada data di bagian ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
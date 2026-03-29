@extends('layouts.app')

@section('content')
<div class="container mt-5 animate__animated animate__fadeIn">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary p-5 text-center text-white border-0 position-relative">
                    <div class="mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->NamaLengkap) }}&background=random&size=128&color=fff" 
                             class="rounded-circle border border-4 border-white shadow-lg" width="120">
                    </div>
                    <h3 class="fw-bold mb-0">{{ Auth::user()->NamaLengkap }}</h3>
                    <span class="badge bg-white text-primary rounded-pill px-3 mt-2 fw-bold">{{ strtoupper(Auth::user()->Role) }}</span>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if(session('success_profil'))
                        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4">
                            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success_profil') }}
                        </div>
                    @endif

                    <form action="{{ route('profil.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-pill px-3"><i class="fa-solid fa-user text-muted"></i></span>
                                    <input type="text" name="NamaLengkap" class="form-control bg-light border-0 rounded-end-pill p-3" value="{{ Auth::user()->NamaLengkap }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Alamat Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 rounded-start-pill px-3"><i class="fa-solid fa-envelope text-muted"></i></span>
                                    <input type="email" name="Email" class="form-control bg-light border-0 rounded-end-pill p-3" value="{{ Auth::user()->Email }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Alamat Rumah</label>
                                <textarea name="Alamat" class="form-control bg-light border-0 rounded-4 p-3" rows="3" required>{{ Auth::user()->Alamat }}</textarea>
                            </div>
                        </div>

                        <div class="mt-5 d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold flex-grow-1 shadow">
                                Simpan Perubahan <i class="fa-solid fa-save ms-2"></i>
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-4 py-3 border fw-bold text-muted">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
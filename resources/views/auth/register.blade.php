@extends('layouts.guest')

@section('content')
<div class="container d-flex align-items-center justify-content-center py-5" style="min-height: 100vh;">
    <div class="card border-0 shadow-lg p-4 p-md-5 animate-up" style="width: 100%; max-width: 550px; border-radius: 24px;">
        <div class="text-center mb-4">
            <h1 class="fw-bold"><span style="color: #ff8c00;">i</span><span style="color: #007bff;">Booku</span></h1>
            <h5 class="fw-bold text-dark">Daftar Akun Baru</h5>
            <p class="text-muted small">Bergabunglah dengan ribuan pembaca lainnya</p>
        </div>

        <form action="/register" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label small fw-bold text-secondary">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa-regular fa-envelope text-muted"></i></span>
                        <input type="email" name="Email" class="form-control border-0 bg-light @error('Email') is-invalid @enderror" placeholder="nama@email.com" value="{{ old('Email') }}" required>
                        @error('Email') <div class="invalid-feedback animate-shake">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label small fw-bold text-secondary">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" name="Password" class="form-control border-0 bg-light @error('Password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                        @error('Password') <div class="invalid-feedback animate-shake">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                    <input type="text" name="NamaLengkap" class="form-control border-0 bg-light" placeholder="Nama Anda" value="{{ old('NamaLengkap') }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-secondary">No. Ponsel</label>
                    <input type="text" name="NoPonsel" class="form-control border-0 bg-light" placeholder="Masukka No Ponsel" value="{{ old('NoPonsel') }}" required>
                </div>

                <div class="col-md-12 mb-4">
                    <label class="form-label small fw-bold text-secondary">Alamat</label>
                    <textarea name="Alamat" class="form-control border-0 bg-light" rows="2" placeholder="Tulis alamat lengkap..." required>{{ old('Alamat') }}</textarea>
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); border: none;">
                    Daftar Sekarang
                </button>
            </div>
            
            <div class="text-center mt-4">
                <p class="small text-muted">Sudah punya akun? <a href="/" class="text-primary text-decoration-none fw-bold">Masuk di sini</a></p>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="successModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-body text-center p-5">
                <div class="wrapper-circle mb-4">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
                <h4 class="fw-bold">Berhasil Terdaftar!</h4>
                <p class="text-muted">Akun iBooku kamu siap digunakan. Silakan login untuk mulai meminjam buku.</p>
                <div class="d-grid mt-4">
                    <a href="/" class="btn btn-primary btn-lg fw-bold" style="border-radius: 12px; background: #7ac142; border: none;">Login Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS Animasi Ceklis & Shake yang sudah kita bahas sebelumnya */
    .wrapper-circle { display: flex; justify-content: center; }
    .checkmark__circle { stroke-dasharray: 166; stroke-dashoffset: 166; stroke-width: 2; stroke-miterlimit: 10; stroke: #7ac142; fill: none; animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards; }
    .checkmark { width: 80px; height: 80px; border-radius: 50%; display: block; stroke-width: 2; stroke: #fff; stroke-miterlimit: 10; box-shadow: inset 0px 0px 0px #7ac142; animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both; }
    .checkmark__check { transform-origin: 50% 50%; stroke-dasharray: 48; stroke-dashoffset: 48; animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards; }
    @keyframes stroke { 100% { stroke-dashoffset: 0; } }
    @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
    @keyframes fill { 100% { box-shadow: inset 0px 0px 0px 40px #7ac142; } }
    .animate-up { animation: fadeInUp 0.6s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-shake { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }
    @keyframes shake { 10%, 90% { transform: translate3d(-1px, 0, 0); } 20%, 80% { transform: translate3d(2px, 0, 0); } 30%, 50%, 70% { transform: translate3d(-4px, 0, 0); } 40%, 60% { transform: translate3d(4px, 0, 0); } }
    .form-control.is-invalid { border-color: #dc3545; background-image: none; }
</style>

@push('scripts')
@if(session('registration_success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('successModal'));
        myModal.show();
    });
</script>
@endif
@endpush
@endsection
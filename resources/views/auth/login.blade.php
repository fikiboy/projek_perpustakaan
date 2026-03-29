@extends('layouts.guest')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card border-0 shadow-lg p-4 p-md-5 animate-up" style="width: 100%; max-width: 420px; border-radius: 24px;">
        
        <div class="text-center mb-4">
            <h1 class="fw-bold"><span class="brand-i" style="color: #ff8c00;">i</span><span class="brand-booku" style="color: #007bff;">Booku</span></h1>
            <h5 class="fw-bold text-dark">Masuk</h5>
            <p class="text-muted small">Selamat datang kembali, Sahabat iBooku!</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger border-0 small py-2 mb-4 animate-shake" style="border-radius: 12px; background-color: #fff5f5; color: #e53e3e;">
                <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Gunakan route('login.post') agar sesuai dengan web.php --}}
        <form action="{{ route('login.post') }}" method="POST" autocomplete="off">
            @csrf
            
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fa-regular fa-envelope text-muted"></i></span>
                    {{-- autocomplete="off" menjaga agar tidak ada saran input lama --}}
                    <input type="email" name="Email" class="form-control border-0 bg-light py-2" placeholder="nama@email.com" autocomplete="off" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-lock text-muted"></i></span>
                    {{-- autocomplete="new-password" paling ampuh mengosongkan input otomatis browser --}}
                    <input type="password" name="Password" class="form-control border-0 bg-light py-2" placeholder="Masukkan kata sandi" autocomplete="new-password" required>
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); border: none;">
                    Masuk Sekarang
                </button>
            </div>

            <div class="position-relative my-4 text-center">
                <hr class="text-muted opacity-25">
                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">Atau</span>
            </div>

            <div class="d-grid">
                <a href="{{ route('google.login') }}" class="btn btn-outline-light py-2 d-flex align-items-center justify-content-center border text-dark shadow-sm" style="border-radius: 12px;">
                    <img src="https://www.gstatic.com/images/branding/product/1x/googleg_48dp.png" alt="Google Logo" style="width: 18px; margin-right: 10px;">
                    <span class="fw-bold small">Masuk dengan Google</span>
                </a>
            </div>
            
            <div class="text-center mt-4">
                <p class="small text-muted">Belum punya akun? <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-bold">Daftar Gratis</a></p>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-up { animation: fadeInUp 0.6s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-shake { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }
    @keyframes shake { 10%, 90% { transform: translate3d(-1px, 0, 0); } 20%, 80% { transform: translate3d(2px, 0, 0); } 30%, 50%, 70% { transform: translate3d(-4px, 0, 0); } 40%, 60% { transform: translate3d(4px, 0, 0); } }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Cek jika ada session 'login_gagal'
    @if(session('login_gagal'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('login_gagal') }}",
            confirmButtonColor: '#007bff',
            showClass: {
                popup: 'animate__animated animate__headShake' // Animasi geleng-geleng kepala (gagal)
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOut'
            }
        });
    @endif
</script>
@endsection
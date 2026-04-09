<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>iBooku - Perpustakaan Digital</title>

   

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

   

    <style>

        :root {

            --ibooku-blue: #007bff;

            --ibooku-orange: #ff8c00;

        }

        body {

            background-color: #f4f7f6;

            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

            padding-bottom: 80px; /* Space for bottom nav */

        }



        .navbar-top {

            background: white;

            border-bottom: 1px solid #eee;

            padding: 10px 0;

        }

        .brand-i { color: var(--ibooku-orange); font-weight: bold; font-size: 24px; }

        .brand-booku { color: var(--ibooku-blue); font-weight: bold; font-size: 24px; }

       

        .search-bar {

            background-color: #f0f2f5;

            border: none;

            border-radius: 20px;

            padding-left: 15px;

            font-size: 14px;

        }



        .header-icons {

            display: flex;

            align-items: center;

            gap: 15px;

        }



        .profile-circle {

            width: 38px;

            height: 38px;

            background-color: #e9ecef;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            transition: 0.3s;

            border: 1px solid #dee2e6;

            cursor: pointer;

        }

        .profile-circle:hover { background-color: #dee2e6; }



        .bottom-nav {

            position: fixed;

            bottom: 0;

            width: 100%;

            background: white;

            border-top: 1px solid #ddd;

            display: flex;

            justify-content: space-around;

            padding: 10px 0;

            z-index: 1000;

        }

        .nav-link-custom {

            text-align: center;

            color: #666;

            text-decoration: none;

            font-size: 11px;

            flex: 1;

            transition: 0.2s;

        }

        .nav-link-custom i {

            display: block;

            font-size: 20px;

            margin-bottom: 2px;

        }

        .nav-link-custom.active { color: var(--ibooku-blue); font-weight: bold; }



        .swal2-popup { border-radius: 20px !important; font-family: 'Segoe UI', sans-serif !important; }

        .locked-screen {
        overflow: hidden !important;
        pointer-events: none !important;
        user-select: none !important;
    }
    /* Kecualikan SweetAlert agar tetap bisa diklik */
    .swal2-container {
        pointer-events: auto !important;
    }
    
    </style>

</head>

<body>



    <nav class="navbar-top sticky-top shadow-sm">

        <div class="container d-flex align-items-center">

            <div class="me-3">

                <span class="brand-i">i</span><span class="brand-booku">Booku</span>

            </div>



            <div class="flex-grow-1 mx-2">

                <form action="{{ route('dashboard') }}" method="GET">

                    <input type="text" name="search" class="form-control search-bar"

                           placeholder="Telusuri Koleksi iBooku..."

                           value="{{ request('search') }}">

                </form>

            </div>



            <div class="header-icons ms-2">

                <div class="dropdown">

                    <div class="profile-circle" id="profileMenu" data-bs-toggle="dropdown" aria-expanded="false">

                        <i class="fa-solid fa-user text-secondary"></i>

                    </div>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" aria-labelledby="profileMenu">

                        <li class="px-3 py-2 border-bottom">

                            <small class="text-muted d-block">Masuk sebagai:</small>

                            <span class="fw-bold">{{ Auth::user()->NamaLengkap }}</span>

                        </li>

                        <li>

                            <a class="dropdown-item py-2" href="{{ route('profil') }}">

                                <i class="fa-solid fa-user-gear me-2 text-primary"></i> Profil Saya

                            </a>

                        </li>

                        @if(Auth::user()->Role == 'administrator')

                        <li>

                            <a class="dropdown-item py-2" href="{{ route('user.index') }}">

                                <i class="fa-solid fa-users-gear me-2 text-success"></i> Data Pengguna

                            </a>

                        </li>

                        @endif

                        <li><hr class="dropdown-divider"></li>

                        <li>

                            <form action="{{ route('logout') }}" method="POST">

                                @csrf

                                <button type="submit" class="dropdown-item py-2 text-danger fw-bold">

                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Logout

                                </button>

                            </form>

                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </nav>



    <main class="container mt-3">

        @yield('content')

    </main>



   <div class="bottom-nav shadow-lg">

    {{-- Menu Beranda, Koleksi, Ulasan tetap sama --}}

    <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">

        <i class="fa-solid fa-house"></i> Beranda

    </a>

    <a href="{{ route('koleksi') }}" class="nav-link-custom {{ request()->routeIs('koleksi') ? 'active' : '' }}">

        <i class="fa-solid fa-book-open"></i> Koleksi

    </a>

    <a href="{{ route('ulasan.index') }}" class="nav-link-custom {{ request()->routeIs('ulasan.index') ? 'active' : '' }}">

        <i class="fa-solid fa-comments"></i> Ulasan

    </a>



    {{-- MENU LAPORAN: Muncul jika Role adalah administrator atau petugas --}}

    @if(Auth::user()->Role == 'administrator' || Auth::user()->Role == 'petugas')

    <a href="{{ route('laporan') }}" class="nav-link-custom {{ request()->routeIs('laporan') ? 'active' : '' }}">

        <i class="fa-solid fa-file-invoice"></i> Laporan

    </a>

    @endif



    {{-- MENU PENGGUNA: Khusus untuk Role administrator --}}

    @if(Auth::user()->Role == 'administrator')

    <a href="{{ route('user.index') }}" class="nav-link-custom {{ request()->routeIs('user.index') ? 'active' : '' }}">

        <i class="fa-solid fa-users-gear"></i> Pengguna

    </a>

    @endif



    <a href="{{ route('profil') }}" class="nav-link-custom {{ request()->routeIs('profil') ? 'active' : '' }}">

        <i class="fa-solid fa-circle-user"></i> Profil

    </a>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // --- LOGIKA POP-UP SUSPEND (SUPER LOCK) ---
    @if(session('suspended'))
        // 1. Tambahkan class pengunci ke body segera
        document.body.classList.add('locked-screen');

        // 2. Bungkus SweetAlert dalam fungsi agar bisa dipanggil berulang kali
        function tampilkanPopUpSuspend() {
            Swal.fire({
                title: 'Akun Terblokir!',
                html: `
                    <div class="text-center">
                        <i class="fa-solid fa-user-slash text-danger mb-3" style="font-size: 50px;"></i>
                        <p>Mohon maaf, akun Anda telah <b>ditangguhkan</b>.</p>
                        <div class="alert alert-secondary text-start" style="font-size: 14px;">
                            <strong>Alasan:</strong><br>
                            {{ session('suspended')['alasan'] }}
                        </div>
                        <p class="small text-muted">Silakan hubungi Admin untuk memulihkan akun Anda.</p>
                    </div>
                `,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#25d366',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fa-brands fa-whatsapp"></i> Hubungi Admin',
                cancelButtonText: '<i class="fa-solid fa-right-from-bracket"></i> Keluar Akun',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buka WhatsApp
                    window.open("https://wa.me/628123456789?text=Halo%20Admin,%20akun%20saya%20({{ session('suspended')['username'] }})%20tersuspend.%20Mohon%20bantuannya.", '_blank');
                    
                    // PANGGIL LAGI FUNGSINYA agar pop-up tidak hilang setelah klik WA
                    tampilkanPopUpSuspend(); 
                } else {
                    // Proses Logout tetap sama (sudah benar)
                    const logoutForm = document.createElement('form');
                    logoutForm.method = 'POST';
                    logoutForm.action = "{{ route('logout') }}";
                    const token = document.createElement('input');
                    token.type = 'hidden';
                    token.name = '_token';
                    token.value = "{{ csrf_token() }}";
                    logoutForm.appendChild(token);
                    document.body.appendChild(logoutForm);
                    logoutForm.submit();
                }
            });
        }

        // Jalankan fungsi saat halaman dimuat
        tampilkanPopUpSuspend();
    @endif
</script>

</body>

</html>
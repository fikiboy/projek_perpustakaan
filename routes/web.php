<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KoleksiController;
use App\Models\Peminjaman;
use App\Models\Buku;
use App\Models\User;
use App\Models\UlasanBuku;
use App\Models\KategoriBuku;
use App\Models\KoleksiPribadi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

// --- GUEST ROUTES ---
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login-proses', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Google Auth
Route::get('/auth/google', function () {
    return Socialite::driver('google')->with(['prompt' => 'select_account'])->redirect();
})->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);

// --- AUTHENTICATED ROUTES ---
Route::middleware(['auth'])->group(function () {
    
    // 1. DASHBOARD
    // 1. DASHBOARD dengan Notifikasi Tugas
Route::get('/dashboard', function (Request $request) {
    $search = $request->query('search');
    $kategoriId = $request->query('kategori'); 
    
    $jumlahPinjam = Peminjaman::where('UserID', Auth::id())
                    ->where('StatusPeminjaman', 'Dipinjam')
                    ->count();

    // --- SISTEM NOTIFIKASI OTOMATIS ---
    $notif = null;

    if (Auth::user()->Role != 'peminjam') {
        // Notif untuk Petugas/Admin: Cek permintaan 'Menunggu'
        $waitingCount = Peminjaman::where('StatusPeminjaman', 'Menunggu')->count();
        if ($waitingCount > 0) {
            $notif = [
                'title' => 'Perhatian Petugas!',
                'text' => "Ada $waitingCount permintaan pinjaman baru yang menunggu persetujuan Anda.",
                'icon' => 'warning',
                'button' => 'Lihat Laporan'
            ];
        }
    } else {
        // Notif untuk Peminjam: Cek buku yang sedang dipinjam
        $activeBorrow = Peminjaman::where('UserID', Auth::id())
                        ->where('StatusPeminjaman', 'Dipinjam')
                        ->count();
        if ($activeBorrow > 0) {
            $notif = [
                'title' => 'Halo Pembaca!',
                'text' => "Kamu sedang meminjam $activeBorrow buku. Jangan lupa jaga kondisi buku dan kembalikan tepat waktu ya!",
                'icon' => 'info',
                'button' => 'Oke, Siap!'
            ];
        }
    }

    $query = Buku::with('kategori');

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('Judul', 'LIKE', "%{$search}%")
              ->orWhere('Penulis', 'LIKE', "%{$search}%");
        });
    }

    if ($kategoriId) {
        $query->where('KategoriID', $kategoriId);
    }

    $buku = $query->get();
    $daftarKategori = KategoriBuku::all();

    return view('dashboard', compact('jumlahPinjam', 'buku', 'search', 'daftarKategori', 'kategoriId', 'notif'));
})->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 2. PINJAM & KEMBALIKAN (Sistem Request/Menunggu)
    Route::get('/buku/detail/{id}', [BukuController::class, 'show'])->name('buku.detail');
    
    Route::post('/buku/pinjam/{id}', function ($id) {
        $buku = Buku::findOrFail($id);
        if ($buku->Stok <= 0) return back()->with('error', 'Maaf, stok habis!');

        $sudahPinjam = Peminjaman::where('UserID', Auth::id())
                        ->where('BukuID', $id)
                        ->whereIn('StatusPeminjaman', ['Menunggu', 'Dipinjam'])
                        ->exists();

        if ($sudahPinjam) return back()->with('error', 'Kamu sudah mengajukan pinjaman atau sedang meminjam buku ini!');

        Peminjaman::create([
            'UserID' => Auth::id(),
            'BukuID' => $id,
            'TanggalPeminjaman' => now(),
            'StatusPeminjaman' => 'Menunggu',
            'TanggalPengembalian' => null 
        ]);

        return back()->with('success_tambah', 'Permintaan pinjam telah terkirim! Menunggu persetujuan petugas.');
    })->name('buku.pinjam.store');

    Route::post('/buku/kembalikan/{id}', function ($id) {
        $pinjam = Peminjaman::findOrFail($id);
        $pinjam->update([
            'StatusPeminjaman' => 'Dikembalikan', 
            'TanggalPengembalian' => now()
        ]);
        $pinjam->buku->increment('Stok');
        return back()->with('success_kembali', 'Buku dikembalikan!');
    })->name('buku.kembalikan');

    Route::get('/peminjaman/bukti/{id}', function ($id) {
        $pinjam = Peminjaman::with(['user', 'buku'])->findOrFail($id);
        return view('buku.bukti_pinjam', compact('pinjam'));
    })->name('peminjaman.bukti');

    // 3. KOLEKSI & FAVORIT
    Route::get('/koleksi', [KoleksiController::class, 'index'])->name('koleksi');
    Route::post('/favorit/{id}', function ($id) {
        $exists = KoleksiPribadi::where('UserID', Auth::id())->where('BukuID', $id)->first();
        if ($exists) { $exists->delete(); return back()->with('success', 'Dihapus dari favorit'); }
        KoleksiPribadi::create(['UserID' => Auth::id(), 'BukuID' => $id]);
        return back()->with('success', 'Ditambahkan ke favorit');
    })->name('buku.favorit');

    // 4. MANAJEMEN BUKU & PEMINJAMAN (Admin & Petugas)
    Route::middleware(['checkRole:administrator,petugas'])->group(function () {
        
        // Approve & Tolak Peminjaman
        Route::put('/peminjaman/approve/{id}', function ($id) {
            $pinjam = Peminjaman::findOrFail($id);
            if ($pinjam->buku->Stok <= 0) return back()->with('error', 'Stok buku habis, tidak bisa approve!');
            
            $pinjam->buku->decrement('Stok');
            $pinjam->update(['StatusPeminjaman' => 'Dipinjam', 'TanggalPeminjaman' => now()]);
            return back()->with('success', 'Peminjaman disetujui!');
        })->name('peminjaman.approve');

        Route::put('/peminjaman/tolak/{id}', function ($id) {
            $pinjam = Peminjaman::findOrFail($id);
            $pinjam->update(['StatusPeminjaman' => 'Ditolak']);
            return back()->with('success', 'Permintaan pinjam ditolak!');
        })->name('peminjaman.tolak');

        // Buku & Kategori
        Route::get('/buku/tambah', function () { 
            $daftarKategori = KategoriBuku::all();
            return view('buku.tambah', compact('daftarKategori')); 
        })->name('buku.tambah');

        Route::post('/buku/simpan', function (Request $request) {
            $data = $request->all();
            if ($request->hasFile('Cover')) {
                $filename = time() . '.' . $request->Cover->extension();
                $request->Cover->move(public_path('covers'), $filename);
                $data['Cover'] = $filename;
            }
            Buku::create($data);
            return redirect('/dashboard')->with('success_tambah', 'Buku berhasil ditambahkan!');
        })->name('buku.simpan');

        Route::get('/buku/edit/{id}', function ($id) {
            $buku = Buku::findOrFail($id);
            $daftarKategori = KategoriBuku::all();
            return view('buku.edit', compact('buku', 'daftarKategori'));
        })->name('buku.edit');

        Route::put('/buku/update/{id}', function (Request $request, $id) {
            $buku = Buku::findOrFail($id);
            $buku->update($request->only(['Judul', 'Penulis', 'Stok', 'KategoriID', 'Penerbit', 'TahunTerbit', 'Deskripsi']));
            if ($request->hasFile('Cover')) {
                $filename = time() . '.' . $request->Cover->extension();
                $request->Cover->move(public_path('covers'), $filename);
                $buku->update(['Cover' => $filename]);
            }
            return redirect('/dashboard')->with('success_tambah', 'Buku berhasil diperbarui!');
        })->name('buku.update');

        Route::delete('/buku/hapus/{id}', function ($id) {
            Buku::destroy($id);
            return back()->with('success_hapus', 'Buku telah dihapus!');
        })->name('buku.hapus');

        Route::get('/kategori', function () {
            return view('admin.kategori', ['kategori' => KategoriBuku::all()]);
        })->name('kategori.index');

        Route::post('/kategori/simpan', function (Request $request) {
            KategoriBuku::create($request->only('NamaKategori'));
            return back()->with('success', 'Kategori berhasil ditambahkan!');
        })->name('kategori.simpan');

        Route::delete('/kategori/hapus/{id}', function ($id) {
            KategoriBuku::destroy($id);
            return back()->with('success', 'Kategori dihapus!');
        })->name('kategori.hapus');
    });

    // 5. LAPORAN & CETAK
    Route::get('/laporan', function (Request $request) {
        if (Auth::user()->Role == 'peminjam') return redirect('/dashboard');
        $laporan = Peminjaman::with(['user', 'buku'])->latest()->get();
        return view('buku.laporan', compact('laporan'));
    })->name('laporan');

    Route::get('/laporan/cetak', function () {
        if (Auth::user()->Role == 'peminjam') return redirect('/dashboard');
        $laporan = Peminjaman::with(['user', 'buku'])->get();
        return view('buku.cetak_laporan', compact('laporan'));
    })->name('laporan.cetak');

    // 6. MANAJEMEN PENGGUNA (Khusus Admin)
    Route::middleware(['checkRole:administrator'])->group(function () {
        Route::get('/pengguna', function () {
            $users = User::all();
            return view('auth.manajemen_user', compact('users'));
        })->name('user.index');

        Route::post('/pengguna/tambah-petugas', function (Request $request) {
            User::create([
                'Username' => $request->Username,
                'Password' => Hash::make($request->Password),
                'Email' => $request->Email,
                'NamaLengkap' => $request->NamaLengkap,
                'Alamat' => $request->Alamat,
                'Role' => 'petugas'
            ]);
            return back()->with('success', 'Petugas berhasil ditambahkan!');
        })->name('user.tambah_petugas');

        Route::put('/pengguna/update-role/{id}', function (Request $request, $id) {
            $user = User::findOrFail($id);
            $user->update(['Role' => $request->Role]);
            return back()->with('success', 'Role berhasil diperbarui!');
        })->name('user.update_role');

        Route::delete('/pengguna/{id}', function ($id) {
            if($id == Auth::id()) return back()->with('error', 'Tidak bisa hapus akun sendiri!');
            User::destroy($id);
            return back()->with('success', 'User dihapus!');
        })->name('user.hapus');
    });

    // 7. ULASAN & 8. PROFIL (Sama seperti sebelumnya)
    Route::get('/ulasan', function () {
        $ulasan = UlasanBuku::where('UserID', Auth::id())->with('buku')->latest()->get();
        return view('buku.ulasan_saya', compact('ulasan'));
    })->name('ulasan.index');

    Route::post('/ulasan/simpan', function (Request $request) {
        UlasanBuku::updateOrCreate(
            ['UserID' => Auth::id(), 'BukuID' => $request->BukuID],
            ['Ulasan' => $request->Ulasan, 'Rating' => $request->Rating]
        );
        return back()->with('popup_ulasan', 'Terima kasih atas ulasannya!');
    })->name('ulasan.store');

    Route::get('/profil', function () { return view('auth.profil'); })->name('profil');
    Route::put('/profil/update', function (Request $request) {
        User::where('UserID', Auth::id())->update($request->only(['NamaLengkap', 'Email', 'Alamat']));
        return back()->with('success_profil', 'Profil diperbarui!');
    })->name('profil.update');
});
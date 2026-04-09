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
    Route::get('/dashboard', function (Request $request) {
        $search = $request->query('search');
        $kategoriId = $request->query('kategori'); 
        
        $jumlahPinjam = Peminjaman::where('UserID', Auth::id())
                        ->where('StatusPeminjaman', 'Dipinjam')
                        ->count();

        $notif = null;

        if (Auth::user()->Role != 'peminjam') {
            $waitingCount = Peminjaman::where('StatusPeminjaman', 'Menunggu')->count();
            $returnCount = Peminjaman::where('StatusPeminjaman', 'Proses Kembali')->count();
            
            if ($waitingCount > 0 || $returnCount > 0) {
                $notif = [
                    'title' => 'Perhatian Petugas!',
                    'text' => "Ada $waitingCount permintaan baru dan $returnCount buku menunggu pengecekan.",
                    'icon' => 'warning',
                    'button' => 'Lihat Laporan'
                ];
            }
        } else {
            $activeBorrow = Peminjaman::where('UserID', Auth::id())
                            ->where('StatusPeminjaman', 'Dipinjam')
                            ->count();
            if ($activeBorrow > 0) {
                $notif = [
                    'title' => 'Halo Pembaca!',
                    'text' => "Kamu sedang meminjam $activeBorrow buku. Jangan lupa kembalikan tepat waktu ya!",
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

    // 2. PINJAM & KEMBALIKAN
    Route::get('/buku/detail/{id}', [BukuController::class, 'show'])->name('buku.detail');
    
    Route::post('/buku/pinjam/{id}', function ($id) {
        $buku = Buku::findOrFail($id);
        if ($buku->Stok <= 0) return back()->with('error', 'Maaf, stok habis!');

        $sudahPinjam = Peminjaman::where('UserID', Auth::id())
                        ->where('BukuID', $id)
                        ->whereIn('StatusPeminjaman', ['Menunggu', 'Dipinjam', 'Proses Kembali'])
                        ->exists();

        if ($sudahPinjam) return back()->with('error', 'Kamu sudah mengajukan pinjaman atau sedang meminjam buku ini!');

        Peminjaman::create([
            'UserID' => Auth::id(),
            'BukuID' => $id,
            'TanggalPeminjaman' => now(),
            'StatusPeminjaman' => 'Menunggu',
            'TanggalPengembalian' => null 
        ]);

        return back()->with('success_tambah', 'Permintaan pinjam terkirim! Menunggu persetujuan.');
    })->name('buku.pinjam.store');

    // Logika Pengembalian (Update ke Controller)
    Route::put('/buku/kembalikan/{id}', [BukuController::class, 'kembalikanBuku'])->name('buku.kembalikan');

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
        
        // Approve & Tolak Peminjaman Awal
        Route::put('/peminjaman/approve/{id}', function ($id) {
            $pinjam = Peminjaman::findOrFail($id);
            if ($pinjam->buku->Stok <= 0) return back()->with('error', 'Stok buku habis!');
            $pinjam->buku->decrement('Stok');
            $pinjam->update(['StatusPeminjaman' => 'Dipinjam', 'TanggalPeminjaman' => now()]);
            return back()->with('success', 'Peminjaman disetujui!');
        })->name('peminjaman.approve');

        Route::put('/peminjaman/tolak/{id}', function ($id) {
            $pinjam = Peminjaman::findOrFail($id);
            $pinjam->update(['StatusPeminjaman' => 'Ditolak']);
            return back()->with('success', 'Permintaan pinjam ditolak!');
        })->name('peminjaman.tolak');

        // FITUR BARU: Approve & Tolak Pengembalian (Sinkron ke BukuController)
        Route::put('/peminjaman/setujui-kembali/{id}', [BukuController::class, 'setujuiKembali'])->name('peminjaman.setujui_kembali');
        Route::put('/peminjaman/tolak-kembali/{id}', [BukuController::class, 'tolakKembali'])->name('peminjaman.tolak_kembali');
        Route::delete('/peminjaman/hapus/{id}', [BukuController::class, 'destroyLaporan'])->name('peminjaman.destroy');

        // CRUD Buku
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

        // Kategori
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

    // 5. LAPORAN
    Route::get('/laporan', function (Request $request) {
        if (Auth::user()->Role == 'peminjam') return redirect('/dashboard');
        $laporan = Peminjaman::with(['user', 'buku'])->latest()->get();
        return view('buku.laporan', compact('laporan'));
    })->name('laporan');

    // 6. MANAJEMEN PENGGUNA (Khusus Admin)
    Route::middleware(['checkRole:administrator'])->group(function () {
        Route::get('/pengguna', function () {
            $users = User::all();
            return view('auth.manajemen_user', compact('users'));
        })->name('user.index');

        Route::put('/user/approve/{id}', [AuthController::class, 'approveUser'])->name('user.approve');
        Route::put('/user/toggle-status/{id}', [AuthController::class, 'toggleStatus'])->name('user.toggle_status');
        Route::delete('/user/hapus/{id}', [AuthController::class, 'hapusUser'])->name('user.hapus');
        
        Route::put('/pengguna/update-role/{id}', function (Request $request, $id) {
            $user = User::findOrFail($id);
            $user->update(['Role' => $request->Role]);
            return back()->with('success', 'Role berhasil diperbarui!');
        })->name('user.update_role');
    });

    // 7. ULASAN & PROFIL
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

    Route::get('/pengguna', [AuthController::class, 'indexUser'])->name('user.index');
Route::post('/pengguna/tambah-petugas', [AuthController::class, 'tambahPetugas'])->name('user.tambah_petugas');
Route::post('/pengguna/toggle-status/{id}', [AuthController::class, 'toggleStatus'])->name('user.toggle_status');
Route::post('/pengguna/update-role/{id}', [AuthController::class, 'updateRole'])->name('user.update_role');
Route::delete('/pengguna/hapus/{id}', [AuthController::class, 'hapusUser'])->name('user.hapus');
});
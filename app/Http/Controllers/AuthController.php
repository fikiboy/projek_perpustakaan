<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // --- AUTHENTICATION METHODS ---

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'Email' => 'required|email|unique:users',
            'Password' => 'required|min:6',
            'NamaLengkap' => 'required',
            'NoPonsel' => 'required',
            'Alamat' => 'required',
        ], [
            'Email.unique' => 'Email ini sudah terdaftar!',
            'Password.min' => 'Kata sandi minimal harus 6 karakter ya!',
        ]);

        User::create([
            'Username' => explode('@', $request->Email)[0], 
            'Email' => $request->Email,
            'Password' => Hash::make($request->Password),
            'NamaLengkap' => $request->NamaLengkap,
            'NoPonsel' => $request->NoPonsel,
            'Alamat' => $request->Alamat,
            'Role' => 'peminjam',
            'Status' => 'Pending', // Pendaftar baru otomatis Pending
        ]);

        return redirect()->route('login')->with('wait_admin', 'Pendaftaran Berhasil! Mohon tunggu persetujuan Admin agar akun Anda aktif.');
    }

    // Cari fungsi login, ubah bagian pengecekan status
public function login(Request $request) {
    $credentials = [
        'Email' => $request->Email,
        'password' => $request->Password,
    ];

    // 1. Biarkan mereka login dulu untuk mengecek password
    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // 2. Cek apakah statusnya Pending
        if ($user->Status === 'Pending') {
            Auth::logout(); // Kalau pending, kita keluarkan
            return back()->with('login_gagal', 'Akun Anda sedang dalam antrean persetujuan.');
        }

        // 3. LOGIKA SUSPEND: Tetap biarkan login, tapi kirim session suspended
        if ($user->Status === 'Ditolak') {
            // Kita tidak logout di sini, agar user masuk ke dashboard 
            // tapi disambut oleh pop-up "Super Lock" di app.blade.php
            return redirect()->intended('/dashboard')->with('suspended', [
                'alasan' => $user->AlasanBlokir ?? 'Pelanggaran pengembalian buku.',
                'username' => $user->Username
            ]);
        }

        // 4. Jika Aktif, masuk normal
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->with('login_gagal', 'Email atau password salah!');
}
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // --- MANAJEMEN USER METHODS ---

    public function indexUser()
    {
        if (Auth::user()->Role == 'peminjam') {
            return redirect('/dashboard');
        }

        $users = User::all();
        return view('auth.manajemen_user', compact('users'));
    }

    /**
     * FUNGSI GANTI STATUS (Baru)
     * Mengaktifkan atau menonaktifkan user langsung dari tabel
     */
    public function toggleStatus($id) {
        $user = User::findOrFail($id);
        
        // Jika status Aktif, ubah jadi Pending (Suspend)
        // Jika status selain Aktif (Pending/NULL), ubah jadi Aktif (Approve)
        $user->Status = ($user->Status == 'Aktif') ? 'Pending' : 'Aktif';
        $user->save();
        
        return back()->with('success', 'Status ' . $user->NamaLengkap . ' berhasil diperbarui!');
    }

    public function tambahPetugas(Request $request)
    {
        $request->validate([
            'Username' => 'required|unique:users',
            'Email' => 'required|email|unique:users',
            'Password' => 'required|min:6',
            'NamaLengkap' => 'required',
            'Alamat' => 'required',
        ]);

        User::create([
            'Username' => $request->Username,
            'Email' => $request->Email,
            'Password' => Hash::make($request->Password),
            'NamaLengkap' => $request->NamaLengkap,
            'Alamat' => $request->Alamat,
            'NoPonsel' => '-', 
            'Role' => 'petugas',
            'Status' => 'Aktif',
        ]);

        return back()->with('success', 'Petugas baru berhasil didaftarkan!');
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->UserID == Auth::id()) {
            return back()->with('error', 'Anda tidak bisa mengubah role sendiri!');
        }

        $request->validate([
            'Role' => 'required|in:peminjam,petugas,administrator'
        ]);

        $user->update(['Role' => $request->Role]);

        return back()->with('success', 'Role ' . $user->NamaLengkap . ' berhasil diubah menjadi ' . $request->Role);
    }

    public function hapusUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->UserID == Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user->delete();
        return back()->with('success', 'Pengguna berhasil dihapus permanen!');
    }

    // --- SOCIALITE / GOOGLE METHODS ---

    public function googleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('Email', $googleUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'Username'    => explode('@', $googleUser->email)[0],
                    'NamaLengkap' => $googleUser->name,
                    'Email'       => $googleUser->email,
                    'Password'    => bcrypt('12345678'),
                    'Role'        => 'peminjam',
                    'NoPonsel'    => '-', 
                    'Alamat'      => '-',
                    'Status'      => 'Aktif',
                ]);
            }

            Auth::login($user);
            request()->session()->regenerate();
            return redirect('/dashboard');

        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Gagal login Google: ' . $e->getMessage());
        }
    }
}
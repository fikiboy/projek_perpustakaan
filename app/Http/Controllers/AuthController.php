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
            'Role' => 'peminjam', // Gunakan lowercase agar konsisten
        ]);

        return back()->with('registration_success', true);
    }

    public function login(Request $request) {
        $credentials = [
            'Email' => $request->Email,
            'password' => $request->Password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->with('login_gagal', 'Akun tidak ditemukan atau kata sandi salah!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // --- MANAJEMEN USER METHODS (TAMBAHAN BARU) ---

    /**
     * Menampilkan halaman daftar pengguna
     */
    public function indexUser()
    {
        // Pastikan hanya admin/petugas yang bisa buka
        if (Auth::user()->Role == 'peminjam') {
            return redirect('/dashboard');
        }

        $users = User::all();
        return view('auth.manajemen_user', compact('users'));
    }

    /**
     * Menambahkan petugas baru oleh Administrator
     */
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
            'NoPonsel' => '-', // Default untuk petugas baru
            'Role' => 'petugas',
        ]);

        return back()->with('success', 'Petugas baru berhasil didaftarkan!');
    }

    /**
     * Mengubah Role Pengguna
     */
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Proteksi: Tidak bisa ubah role diri sendiri
        if ($user->UserID == Auth::id()) {
            return back()->with('error', 'Anda tidak bisa mengubah role sendiri!');
        }

        $request->validate([
            'Role' => 'required|in:peminjam,petugas,administrator'
        ]);

        $user->update([
            'Role' => $request->Role
        ]);

        return back()->with('success', 'Role ' . $user->NamaLengkap . ' berhasil diubah menjadi ' . $request->Role);
    }

    /**
     * Menghapus Pengguna
     */
    public function hapusUser($id)
    {
        $user = User::findOrFail($id);

        // Proteksi: Tidak bisa hapus diri sendiri
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
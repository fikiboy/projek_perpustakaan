<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\KoleksiPribadi; // Import model KoleksiPribadi (Favorit)
use Illuminate\Support\Facades\Auth;

class KoleksiController extends Controller
{
    public function index()
    {
        $userID = Auth::id();

        // 1. Mengambil data peminjaman (Buku yang sedang/pernah dipinjam)
        // Eager loading 'buku.ulasan' untuk performa yang lebih baik
        $koleksi = Peminjaman::with(['buku.ulasan'])
            ->where('UserID', $userID)
            ->orderBy('PeminjamanID', 'desc')
            ->get();

        // 2. Mengambil data buku favorit (Koleksi Pribadi)
        // Relasi 'buku' harus sudah didefinisikan di model KoleksiPribadi
        $favorit = KoleksiPribadi::with(['buku.ulasan'])
            ->where('UserID', $userID)
            ->orderBy('KoleksiID', 'desc')
            ->get();

        // Mengirimkan kedua variabel ke view buku.koleksi
        return view('buku.koleksi', compact('koleksi', 'favorit'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\UlasanBuku;
use Illuminate\Support\Facades\Auth;

class BukuController extends Controller
{
    // Halaman Detail Buku
    public function show($id)
    {
        $buku = Buku::findOrFail($id);
        // Mengambil ulasan beserta user yang memberi ulasan
        $ulasan = UlasanBuku::where('BukuID', $id)->with('user')->get();
        return view('buku.detail', compact('buku', 'ulasan'));
    }

    // Halaman Form Peminjaman
    public function pinjamForm($id)
    {
        $buku = Buku::findOrFail($id);

        // Tambahan: Cek ketersediaan buku sebelum menampilkan form
        $cek = Peminjaman::where('BukuID', $id)->where('StatusPeminjaman', 'Dipinjam')->exists();
        if ($cek) {
            return redirect()->route('dashboard')->with('error', 'Maaf, buku ini sedang dipinjam.');
        }

        return view('buku.pinjam_form', compact('buku'));
    }

    // Proses Simpan Peminjaman (Sudah Digabung & Rapih)
    public function storePeminjaman(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'TanggalPengembalian' => 'required|date|after:today',
        ]);

        // 2. Cek Ketersediaan (Keamanan ganda jika user tembak URL)
        $cek = Peminjaman::where('BukuID', $id)->where('StatusPeminjaman', 'Dipinjam')->exists();
        
        if ($cek) {
            return redirect()->route('dashboard')->with('error', 'Maaf, buku ini baru saja dipinjam orang lain.');
        }

        // 3. Simpan Data Peminjaman
        Peminjaman::create([
            'UserID' => Auth::id(),
            'BukuID' => $id,
            'TanggalPeminjaman' => now(),
            'TanggalPengembalian' => $request->TanggalPengembalian,
            'StatusPeminjaman' => 'Dipinjam',
        ]);

        return redirect()->route('dashboard')->with('success_pinjam', 'Buku berhasil dipinjam! Silakan cek menu Koleksi.');
    }
}
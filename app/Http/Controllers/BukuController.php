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
        $ulasan = UlasanBuku::where('BukuID', $id)->with('user')->get();
        return view('buku.detail', compact('buku', 'ulasan'));
    }

    // Halaman Form Peminjaman
    public function pinjamForm($id)
    {
        $buku = Buku::findOrFail($id);

        $cek = Peminjaman::where('BukuID', $id)->where('StatusPeminjaman', 'Dipinjam')->exists();
        if ($cek) {
            return redirect()->route('dashboard')->with('error', 'Maaf, buku ini sedang dipinjam.');
        }

        return view('buku.pinjam_form', compact('buku'));
    }

    // Proses Simpan Peminjaman
    public function storePeminjaman(Request $request, $id)
    {
        $request->validate([
            'TanggalPengembalian' => 'required|date|after:today',
        ]);

        $cek = Peminjaman::where('BukuID', $id)->where('StatusPeminjaman', 'Dipinjam')->exists();
        
        if ($cek) {
            return redirect()->route('dashboard')->with('error', 'Maaf, buku ini baru saja dipinjam orang lain.');
        }

        Peminjaman::create([
            'UserID' => Auth::id(),
            'BukuID' => $id,
            'TanggalPeminjaman' => now(),
            'TanggalPengembalian' => $request->TanggalPengembalian,
            'StatusPeminjaman' => 'Dipinjam',
        ]);

        return redirect()->route('dashboard')->with('success_pinjam', 'Buku berhasil dipinjam! Silakan cek menu Koleksi.');
    }

    // --- FITUR PENGEMBALIAN UNTUK USER ---
    public function kembalikanBuku($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // Update status menjadi menunggu pengecekan petugas
        $peminjaman->update([
            'StatusPeminjaman' => 'Menunggu Pengecekan',
        ]);

        return redirect()->back()->with('info_proses', 'Mohon tunggu, buku sedang dicek oleh petugas.');
    }

    // --- FITUR APPROVAL UNTUK PETUGAS/ADMIN ---
    
    // 1. Jika Petugas Setuju
    public function setujuiKembali($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        $peminjaman->update([
            'StatusPeminjaman' => 'Dikembalikan',
            'TanggalPengembalian' => now(), 
        ]);

        // Stok buku bertambah kembali otomatis
        $peminjaman->buku->increment('Stok');

        return redirect()->back()->with('success', 'Pengembalian disetujui dan stok buku telah bertambah!');
    }

    // 2. Jika Petugas Tolak (Otomatis Suspend User)
    public function tolakKembali(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        $peminjaman->update([
            'StatusPeminjaman' => 'Ditolak'
        ]);

        $user = $peminjaman->user;
        $user->update([
            'Status' => 'Ditolak',
            'AlasanBlokir' => $request->alasan 
        ]);

        return redirect()->back()->with('success', 'Pengembalian ditolak! Akun user otomatis ditangguhkan.');
    }

    public function destroyLaporan($id)
{
    $peminjaman = Peminjaman::findOrFail($id);
    $peminjaman->delete();

    return redirect()->back()->with('success', 'Laporan berhasil dihapus dari database.');
}
} // Penutup Class
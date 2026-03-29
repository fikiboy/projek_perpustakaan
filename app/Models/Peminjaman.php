<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    // Kunci nama tabel karena di database UKK biasanya namanya 'peminjaman' (bukan peminjamans)
    protected $table = 'peminjaman';

    // Kunci Primary Key jika bukan 'id'
    protected $primaryKey = 'PeminjamanID';

    // Matikan timestamps jika tabelmu tidak punya kolom created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
    'UserID', 
    'BukuID', 
    'TanggalPeminjaman', 
    'TanggalPengembalian', // Pastikan ini ada
    'StatusPeminjaman'
];

    // INI BAGIAN YANG PENTING: Relasi ke model Buku
    public function buku()
    {
        // Parameter: (NamaModel, ForeignKeyDiTabelIni, OwnerKeyDiTabelBuku)
        return $this->belongsTo(Buku::class, 'BukuID', 'BukuID');
    }

    // Tambahan relasi ke User (opsional tapi berguna untuk admin)
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
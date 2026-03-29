<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';
    protected $primaryKey = 'BukuID';
  protected $fillable = ['Judul', 'Penulis', 'Penerbit', 'TahunTerbit', 'Cover', 'Stok', 'KategoriID'];

    // Relasi ke peminjaman (opsional)
    public function peminjaman() {
        return $this->hasMany(Peminjaman::class, 'BukuID', 'BukuID');
    }

    public function kategori()
{
    return $this->belongsTo(KategoriBuku::class, 'KategoriID');
}
public function ulasan()
{
    // Penting agar $buku->ulasan bisa dipanggil di detail buku
    return $this->hasMany(UlasanBuku::class, 'BukuID', 'BukuID');
}

public function isFavorit()
{
    return $this->hasOne(KoleksiPribadi::class, 'BukuID', 'BukuID')
                ->where('UserID', auth()->id());
}
}
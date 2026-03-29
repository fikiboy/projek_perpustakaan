<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UlasanBuku extends Model
{
    use HasFactory;

    protected $table = 'ulasanbuku';
    protected $primaryKey = 'UlasanID';

    // Jika tabelmu tidak pakai created_at/updated_at, set false
    // Tapi jika error 'created_at' not found muncul, biarkan atau tambahkan kolomnya
    public $timestamps = true; 

    protected $fillable = ['UserID', 'BukuID', 'Ulasan', 'Rating'];

    // INI YANG KURANG: Relasi ke model Buku
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'BukuID', 'BukuID');
    }

    // Relasi ke User (untuk menampilkan siapa yang memberi ulasan)
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
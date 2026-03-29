<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBuku extends Model
{
    use HasFactory;

    protected $table = 'kategoribuku'; // Sesuaikan dengan nama tabel di DB kamu
    protected $primaryKey = 'KategoriID'; // Sesuaikan dengan PK di DB kamu
    protected $fillable = ['NamaKategori'];

    // Relasi ke Buku
    public function buku()
    {
        return $this->hasMany(Buku::class, 'KategoriID');
    }
}
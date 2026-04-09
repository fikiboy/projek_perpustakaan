<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'UserID';

    protected $fillable = [
    'Username', 
    'Email', 
    'Password', 
    'NamaLengkap', 
    'Alamat', 
    'NoPonsel', 
    'Role',
    'Status',        // Pastikan ada
    'AlasanBlokir'
        ];

    // Beritahu Laravel kolom password kita namanya 'Password'
    public function getAuthPassword()
    {
        return $this->Password;
    }

    // Tambahkan ini di dalam class User
public function peminjaman()
{
    // Pastikan nama tabel/model peminjaman kamu sesuai
    return $this->hasMany(Peminjaman::class, 'UserID', 'UserID');
}
}
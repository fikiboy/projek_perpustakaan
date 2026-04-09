<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSuspended
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->Status == 'Ditolak') {
            $user = Auth::user();
            $alasan = $user->AlasanBlokir ?? 'Pelanggaran peraturan perpustakaan.';
            
            // Simpan data suspend ke session agar bisa dibaca app.blade.php
            session()->flash('suspend_error', [
                'title' => 'Akun Ditangguhkan!',
                'text' => "Alasan: $alasan. Silakan hubungi admin untuk pembukaan akun.",
                'wa_link' => "https://wa.me/628123456789?text=Halo%20Admin,%20akun%20saya%20(Username:%20$user->Username)%20tersuspend.%20Mohon%20bantuannya."
            ]);

            // Jangan di-logout di sini supaya app.blade.php bisa baca session-nya dulu
            // Kita akan paksa logout via JavaScript di app.blade.php nanti
        }

        return $next($request);
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Jika kolom Status belum ada, buat baru. Jika sudah ada, ubah tipenya.
        if (!Schema::hasColumn('users', 'Status')) {
            $table->enum('Status', ['Pending', 'Aktif', 'Ditolak'])->default('Pending');
        } else {
            // Gunakan string jika tidak ingin install doctrine/dbal, atau tetap enum jika sudah ada
            $table->string('Status')->default('Pending')->change(); 
        }

        // Tambahkan kolom AlasanBlokir jika belum ada
        if (!Schema::hasColumn('users', 'AlasanBlokir')) {
            $table->text('AlasanBlokir')->nullable()->after('Status');
        }
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Cek dulu baru hapus, supaya tidak error Syntax Error 1091 lagi
        if (Schema::hasColumn('users', 'AlasanBlokir')) {
            $table->dropColumn('AlasanBlokir');
        }
        
        // Kembalikan Status ke default awal jika perlu
        if (Schema::hasColumn('users', 'Status')) {
            $table->enum('Status', ['Pending', 'Aktif'])->default('Pending')->change();
        }
    });
}
};

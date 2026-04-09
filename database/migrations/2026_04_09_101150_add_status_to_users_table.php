<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Tambahkan 'Ditolak' ke dalam list enum
        $table->enum('Status', ['Pending', 'Aktif', 'Ditolak'])->default('Pending')->change();
        
        // Tambahkan kolom alasan blokir agar pop-up bisa menampilkan alasan spesifik
        $table->text('AlasanBlokir')->nullable()->after('Status');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Balikkan ke kondisi awal jika diperlukan
        $table->dropColumn(['AlasanBlokir']);
        $table->enum('Status', ['Pending', 'Aktif'])->default('Pending')->change();
    });
}
};

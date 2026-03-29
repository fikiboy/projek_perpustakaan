<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            // Menambahkan kolom Cover setelah TahunTerbit
            // nullable() artinya boleh kosong jika buku tidak punya gambar
            $table->string('Cover')->nullable()->after('TahunTerbit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            // Menghapus kembali kolom Cover jika migrasi di-rollback
            $table->dropColumn('Cover');
        });
    }
};
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
        // Menambahkan kolom Stok dengan tipe data integer
        $table->integer('Stok')->default(1)->after('TahunTerbit');
    });
}

public function down(): void
{
    Schema::table('buku', function (Blueprint $table) {
        $table->dropColumn('Stok');
    });
}
};

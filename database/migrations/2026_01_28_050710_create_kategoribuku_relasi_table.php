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
    Schema::create('kategori_buku', function (Blueprint $table) {
        $table->id('KategoriID'); // Primary Key
        $table->string('NamaKategori', 100);
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('kategoribuku_relasi');
    }
};
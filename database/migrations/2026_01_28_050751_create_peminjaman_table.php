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
    Schema::create('peminjaman', function (Blueprint $table) {
        $table->id('PeminjamanID');
        $table->unsignedBigInteger('UserID');
        $table->unsignedBigInteger('BukuID');
        $table->date('TanggalPeminjaman');
        $table->date('TanggalPengembalian');
        $table->string('StatusPeminjaman'); // Dipinjam/Dikembalikan
        
        $table->foreign('UserID')->references('UserID')->on('users')->onDelete('cascade');
        $table->foreign('BukuID')->references('BukuID')->on('buku')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};

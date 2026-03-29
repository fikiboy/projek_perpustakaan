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
    Schema::create('koleksipribadi', function (Blueprint $table) {
        $table->id('KoleksiID');
        $table->unsignedBigInteger('UserID');
        $table->unsignedBigInteger('BukuID');
        
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
        Schema::dropIfExists('koleksipribadi');
    }
};

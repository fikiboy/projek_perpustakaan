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
        // 1. TABEL USERS SESUAI MOCKUP iBooku DAN DOKUMEN UKK
        Schema::create('users', function (Blueprint $table) {
            $table->id('UserID'); // Primary Key
            $table->string('Username')->unique();
            $table->string('Password');
            $table->string('Email')->unique();
            $table->string('NamaLengkap');
            $table->string('NoPonsel'); // Kolom NoPonsel (Wajib ada agar tidak error)
            $table->text('Alamat'); // Mencakup Kecamatan
            $table->enum('Role', ['administrator', 'petugas', 'peminjam'])->default('peminjam'); 
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. TABEL TOKEN RESET PASSWORD (BAWAAN LARAVEL)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. TABEL SESSIONS (UNTUK MENANGANI LOGIN USER)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
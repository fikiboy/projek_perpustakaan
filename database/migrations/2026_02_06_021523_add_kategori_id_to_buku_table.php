<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('buku', function (Blueprint $table) {
        // Tambah kolom KategoriID sebagai Foreign Key
        $table->unsignedBigInteger('KategoriID')->nullable()->after('BukuID');
        
        // Opsional: Hubungkan secara formal (Foreign Key Constraint)
        $table->foreign('KategoriID')->references('KategoriID')->on('kategoribuku')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('buku', function (Blueprint $table) {
        $table->dropForeign(['KategoriID']);
        $table->dropColumn('KategoriID');
    });
}
};

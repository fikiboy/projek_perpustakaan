<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    \App\Models\KategoriBuku::insert([
        ['NamaKategori' => 'Novel'],
        ['NamaKategori' => 'Edukasi'],
        ['NamaKategori' => 'Komik'],
        ['NamaKategori' => 'Sains'],
    ]);
}
}

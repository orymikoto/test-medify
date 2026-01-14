<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kategoris = [
            ['nama' => 'Obat Generik', 'kode' => 'OBT-GEN'],
            ['nama' => 'Obat Paten', 'kode' => 'OBT-PAT'],
            ['nama' => 'Alat Kesehatan', 'kode' => 'ALKES'],
            ['nama' => 'Material Kesehatan', 'kode' => 'MATKES'],
            ['nama' => 'Alat Tulis Kantor', 'kode' => 'ATK'],
            ['nama' => 'Produk Umum', 'kode' => 'UMUM'],
            ['nama' => 'Vitamin & Suplemen', 'kode' => 'VIT'],
            ['nama' => 'Perawatan Kulit', 'kode' => 'SKIN'],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\MasterItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suppliers = ['Tokopaedi', 'Bukulapuk', 'TokoBagas', 'E Commurz', 'Blublu'];
        $jenis = ['Obat', 'Alkes', 'Matkes', 'Umum', 'ATK'];

        $items = [
            ['nama' => 'Paracetamol 500mg', 'harga_beli' => 5000, 'laba' => 20, 'supplier' => $suppliers[0], 'jenis' => $jenis[0]],
            ['nama' => 'Amoxicillin 500mg', 'harga_beli' => 15000, 'laba' => 25, 'supplier' => $suppliers[0], 'jenis' => $jenis[0]],
            ['nama' => 'Ibuprofen 400mg', 'harga_beli' => 8000, 'laba' => 22, 'supplier' => $suppliers[1], 'jenis' => $jenis[0]],
            ['nama' => 'Termometer Digital', 'harga_beli' => 50000, 'laba' => 30, 'supplier' => $suppliers[2], 'jenis' => $jenis[1]],
            ['nama' => 'Tensimeter Digital', 'harga_beli' => 250000, 'laba' => 35, 'supplier' => $suppliers[2], 'jenis' => $jenis[1]],
            ['nama' => 'Stetoskop', 'harga_beli' => 150000, 'laba' => 30, 'supplier' => $suppliers[3], 'jenis' => $jenis[1]],
            ['nama' => 'Masker Medis', 'harga_beli' => 2000, 'laba' => 50, 'supplier' => $suppliers[4], 'jenis' => $jenis[2]],
            ['nama' => 'Sarung Tangan Lateks', 'harga_beli' => 1500, 'laba' => 40, 'supplier' => $suppliers[4], 'jenis' => $jenis[2]],
            ['nama' => 'Pulpen', 'harga_beli' => 3000, 'laba' => 15, 'supplier' => $suppliers[0], 'jenis' => $jenis[4]],
            ['nama' => 'Buku Catatan', 'harga_beli' => 10000, 'laba' => 20, 'supplier' => $suppliers[1], 'jenis' => $jenis[4]],
            ['nama' => 'Vitamin C 1000mg', 'harga_beli' => 25000, 'laba' => 30, 'supplier' => $suppliers[2], 'jenis' => $jenis[0]],
            ['nama' => 'Multivitamin', 'harga_beli' => 35000, 'laba' => 28, 'supplier' => $suppliers[3], 'jenis' => $jenis[0]],
            ['nama' => 'Hand Sanitizer', 'harga_beli' => 15000, 'laba' => 25, 'supplier' => $suppliers[4], 'jenis' => $jenis[2]],
            ['nama' => 'Alkohol 70%', 'harga_beli' => 20000, 'laba' => 30, 'supplier' => $suppliers[0], 'jenis' => $jenis[2]],
            ['nama' => 'Kapas Steril', 'harga_beli' => 5000, 'laba' => 20, 'supplier' => $suppliers[1], 'jenis' => $jenis[2]],
        ];

        foreach ($items as $index => $item) {
            $kode = str_pad($index + 1, 5, '0', STR_PAD_LEFT);
            MasterItem::create([
                'kode' => $kode,
                'nama' => $item['nama'],
                'harga_beli' => $item['harga_beli'],
                'laba' => $item['laba'],
                'supplier' => $item['supplier'],
                'jenis' => $item['jenis'],
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\KategoriItem;
use App\Models\MasterItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all kategoris and master items
        $kategoris = Kategori::all();
        $masterItems = MasterItem::all();

        if ($kategoris->isEmpty() || $masterItems->isEmpty()) {
            $this->command->warn('Please run KategoriSeeder and MasterItemSeeder first!');
            return;
        }

        // Map items to categories based on their jenis/nama
        $categoryMapping = [
            'Obat Generik' => ['Paracetamol', 'Amoxicillin', 'Ibuprofen'],
            'Obat Paten' => ['Vitamin C', 'Multivitamin'],
            'Alat Kesehatan' => ['Termometer', 'Tensimeter', 'Stetoskop'],
            'Material Kesehatan' => ['Masker', 'Sarung Tangan', 'Hand Sanitizer', 'Alkohol', 'Kapas'],
            'Alat Tulis Kantor' => ['Pulpen', 'Buku'],
            'Vitamin & Suplemen' => ['Vitamin C', 'Multivitamin'],
            'Perawatan Kulit' => ['Hand Sanitizer'],
            'Produk Umum' => ['Hand Sanitizer', 'Alkohol'],
        ];

        foreach ($categoryMapping as $kategoriNama => $itemKeywords) {
            $kategori = $kategoris->where('nama', $kategoriNama)->first();
            
            if (!$kategori) {
                continue;
            }

            // Find master items that match the keywords
            foreach ($masterItems as $masterItem) {
                foreach ($itemKeywords as $keyword) {
                    if (stripos($masterItem->nama, $keyword) !== false) {
                        // Check if relationship already exists
                        $exists = KategoriItem::where('kategori_id', $kategori->id)
                            ->where('master_item_id', $masterItem->id)
                            ->exists();

                        if (!$exists) {
                            KategoriItem::create([
                                'kategori_id' => $kategori->id,
                                'master_item_id' => $masterItem->id,
                            ]);
                        }
                        break; // Found a match, move to next item
                    }
                }
            }
        }
    }
}

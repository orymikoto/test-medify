<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'medify@example.com',
            'password' => Hash::make("password")
        ]);

        // Seed in order: Kategori -> MasterItem -> KategoriItem (many-to-many)
        $this->call([
            KategoriSeeder::class,
            MasterItemSeeder::class,
            KategoriItemSeeder::class,
        ]);
    }
}

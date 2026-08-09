<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's categories.
     */
    public function run(): void
    {
        $categories = [
            ['Fiksi', 'fiksi', 'FIK'],
            ['Non-Fiksi', 'non-fiksi', 'NFK'],
            ['Referensi', 'referensi', 'REF'],
            ['Buku Paket', 'buku-paket', 'PAK'],
            ['Ensiklopedia', 'ensiklopedia', 'ENS'],
            ['Biografi', 'biografi', 'BIO'],
            ['Agama', 'agama', 'AGM'],
            ['Sains & Teknologi', 'sains-teknologi', 'SAI'],
            ['Sejarah', 'sejarah', 'SEJ'],
            ['Majalah & Jurnal', 'majalah-jurnal', 'MAJ'],
        ];

        foreach ($categories as [$name, $slug, $prefix]) {
            Category::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'prefix' => $prefix]
            );
        }
    }
}

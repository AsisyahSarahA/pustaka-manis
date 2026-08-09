<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    /**
     * Seed the application's books with real school library data.
     *
     * Note: BookObserver dinonaktifkan via withoutEvents() agar pembuatan
     * book_items ditangani manual oleh nested loop di bawah (barcode UUID).
     */
    public function run(): void
    {
        $pelajaran = Category::firstOrCreate(
            ['slug' => 'buku-pelajaran'],
            ['name' => 'Buku Pelajaran', 'prefix' => 'BPL', 'description' => 'Buku teks pelajaran sekolah']
        );

        $referensi = Category::firstOrCreate(
            ['slug' => 'buku-referensi'],
            ['name' => 'Buku Referensi', 'prefix' => 'BFR', 'description' => 'Buku referensi dan penunjang']
        );

        $books = [
            ['title' => 'Cerdas Aktif Matematika Kls VII', 'author' => 'Kemdikbud', 'publisher' => 'Pusat', 'year' => '2005', 'stock' => 75, 'category' => $pelajaran],
            ['title' => 'Cerdas Aktif Matematika Kls VIII', 'author' => 'Kemdikbud', 'publisher' => 'Pusat', 'year' => '2005', 'stock' => 58, 'category' => $pelajaran],
            ['title' => 'Functional English for Junior High School Grade VII', 'author' => 'Kemdikbud', 'publisher' => 'Pusat', 'year' => '2005', 'stock' => 74, 'category' => $pelajaran],
            ['title' => 'Pelajaran Bahasa dan Sastra Indonesia VII', 'author' => 'Kemdikbud', 'publisher' => 'Pusat', 'year' => '2006', 'stock' => 64, 'category' => $pelajaran],
            ['title' => 'Agama Islam Kelas VII', 'author' => 'Kemendikbudristek', 'publisher' => 'CV Media Nusa Gemilang', 'year' => '2022', 'stock' => 2, 'category' => $pelajaran],
            ['title' => 'Matematika Kelas VIII', 'author' => 'Kemendikbudristek', 'publisher' => 'CV Media Nusa Gemilang', 'year' => '2022', 'stock' => 2, 'category' => $pelajaran],
            ['title' => 'Teladan Pancasila Presiden Indonesia', 'author' => 'Tim Penyusun', 'publisher' => 'Erlangga / CV Noura', 'year' => '2026', 'stock' => 1, 'category' => $referensi],
            ['title' => 'Uang Untuk Pemula', 'author' => 'Tim Penyusun', 'publisher' => 'Erlangga', 'year' => '2026', 'stock' => 1, 'category' => $referensi],
            ['title' => 'Wirausaha Untuk Pemula', 'author' => 'Tim Penyusun', 'publisher' => 'Erlangga', 'year' => '2026', 'stock' => 1, 'category' => $referensi],
        ];

        $racks = ['Rak A1', 'Rak A2', 'Rak A3', 'Rak B1', 'Rak B2', 'Rak C1'];
        $yearCounters = [];

        foreach ($books as $data) {
            $year = $data['year'];
            $yearCounters[$year] = ($yearCounters[$year] ?? 0) + 1;
            $bookCode = sprintf('B-%s-%03d', $year, $yearCounters[$year]);

            $book = Book::withoutEvents(fn () => Book::create([
                'book_code' => $bookCode,
                'title' => $data['title'],
                'category_id' => $data['category']->id,
                'author' => $data['author'],
                'publisher' => $data['publisher'],
                'publication_year' => $year,
                'rack_location' => $racks[array_rand($racks)],
                'total_stock' => $data['stock'],
                'available_stock' => $data['stock'],
                'is_active' => true,
            ]));

            // Nested loop: buat book_items sebanyak total_stock
            for ($i = 1; $i <= $data['stock']; $i++) {
                BookItem::create([
                    'book_id' => $book->id,
                    'item_code' => $bookCode . '-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                    'barcode' => (string) Str::uuid(),
                    'condition' => 'baik',
                    'status' => 'tersedia',
                ]);
            }

            $this->command?->info("Buku '{$data['title']}' => {$bookCode} ({$data['stock']} eksemplar)");
        }
    }
}

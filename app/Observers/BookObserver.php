<?php

namespace App\Observers;

use App\Models\Book;
use App\Models\BookItem;
use App\Services\CodeGenerator;

class BookObserver
{
    /**
     * Handle the Book "created" event.
     */
    public function created(Book $book): void
    {
        for ($i = 1; $i <= $book->total_stock; $i++) {
            $itemCode = CodeGenerator::generateItemCode($book->book_code, $i);

            BookItem::create([
                'book_id' => $book->id,
                'item_code' => $itemCode,
                'barcode' => $itemCode,
                'condition' => 'baik',
                'status' => 'tersedia',
            ]);
        }

        $book->forceFill([
            'available_stock' => $book->total_stock,
        ])->save();
    }
}

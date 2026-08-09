<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'book_item_id',
        'status',
        'return_date',
        'fine_amount',
    ];

    public const STATUSES = ['dipinjam', 'dikembalikan', 'hilang'];

    protected function casts(): array
    {
        return [
            'return_date' => 'date',
            'fine_amount' => 'integer',
        ];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function bookItem(): BelongsTo
    {
        return $this->belongsTo(BookItem::class);
    }
}

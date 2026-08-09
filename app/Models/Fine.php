<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_item_id',
        'member_id',
        'user_id',
        'amount',
        'status',
        'fine_date',
        'payment_date',
        'receipt_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'fine_date' => 'date',
            'payment_date' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function loanItem(): BelongsTo
    {
        return $this->belongsTo(LoanItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

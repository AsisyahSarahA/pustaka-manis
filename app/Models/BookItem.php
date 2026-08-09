<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'item_code',
        'barcode',
        'condition',
        'status',
    ];

    public const CONDITIONS = ['baik', 'rusak', 'hilang'];
    public const STATUSES = ['tersedia', 'dipinjam', 'perbaikan'];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'tersedia' && $this->condition === 'baik';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'tersedia' => 'Tersedia',
            'dipinjam' => 'Dipinjam',
            'perbaikan' => 'Dalam Perbaikan',
            default => ucfirst($this->status),
        };
    }

    public function getConditionLabelAttribute(): string
    {
        return match ($this->condition) {
            'baik' => 'Baik',
            'rusak' => 'Rusak',
            'hilang' => 'Hilang',
            default => ucfirst($this->condition),
        };
    }
}

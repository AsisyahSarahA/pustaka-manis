<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_code',
        'title',
        'category_id',
        'author',
        'publisher',
        'publication_year',
        'rack_location',
        'total_stock',
        'available_stock',
        'cover_image',
        'is_active',
    ];

    public function getCoverUrlAttribute(): ?string
    {
        if ($this->cover_image && file_exists(public_path($this->cover_image))) {
            return asset($this->cover_image);
        }
        return null;
    }

    protected function casts(): array
    {
        return [
            'total_stock' => 'integer',
            'available_stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookItem::class);
    }

    public function availableItems(): HasMany
    {
        return $this->hasMany(BookItem::class)->where('status', 'tersedia');
    }

    public function borrowedItems(): HasMany
    {
        return $this->hasMany(BookItem::class)->where('status', 'dipinjam');
    }
}

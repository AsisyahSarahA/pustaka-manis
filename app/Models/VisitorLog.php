<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_type',
        'member_id',
        'guest_name',
        'guest_origin',
        'purpose',
        'visit_date',
        'check_in_time',
    ];

    public const TYPES = ['siswa', 'guru', 'tamu'];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function getVisitorTypeLabelAttribute(): string
    {
        return match ($this->visitor_type) {
            'siswa' => 'Siswa',
            'guru' => 'Guru',
            'tamu' => 'Tamu',
            default => ucfirst($this->visitor_type),
        };
    }
}

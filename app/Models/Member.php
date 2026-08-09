<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_code',
        'name',
        'type',
        'identity_number',
        'department_class',
        'phone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public const TYPES = ['siswa', 'guru', 'staf', 'eksternal'];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function visitorLogs(): HasMany
    {
        return $this->hasMany(VisitorLog::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'siswa' => 'Siswa',
            'guru' => 'Guru',
            'staf' => 'Staf',
            'eksternal' => 'Eksternal',
            default => ucfirst($this->type),
        };
    }
}

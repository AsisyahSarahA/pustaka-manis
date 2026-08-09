<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_code',
        'member_id',
        'user_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
    ];

    protected $appends = ['is_late'];

    public const STATUSES = ['berjalan', 'terlambat', 'selesai', 'dibatalkan'];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'berjalan' => 'Berjalan',
            'terlambat' => 'Terlambat',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Cek apakah peminjaman sudah melewati jatuh tempo.
     */
    public function getIsLateAttribute(): bool
    {
        return Carbon::parse($this->due_date)->isPast()
            && $this->status === 'berjalan';
    }

    /**
     * Hitung jumlah hari terlambat.
     */
    public function getLateDaysAttribute(): int
    {
        if (!$this->is_late) {
            return 0;
        }

        $due = Carbon::parse($this->due_date)->startOfDay();
        $today = Carbon::now()->startOfDay();

        if ($today->lte($due)) {
            return 0;
        }

        return (int) abs($due->diffInDays($today));
    }
}

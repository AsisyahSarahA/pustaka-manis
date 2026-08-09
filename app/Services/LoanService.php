<?php

namespace App\Services;

use App\Models\Member;

class LoanService
{
    /**
     * Ambil batas kuota dan durasi pinjam berdasar tipe anggota dari settings.
     */
    public static function quotaFor(Member $member): int
    {
        return (int) setting("max_loan_{$member->type}", 2);
    }

    public static function durationFor(Member $member): int
    {
        return (int) setting("loan_days_{$member->type}", $member->type === 'siswa' ? 7 : 14);
    }

    /**
     * Jumlah pinjaman berjalan milik member.
     */
    public static function activeLoanCount(Member $member): int
    {
        return $member->loans()
            ->whereIn('status', ['berjalan', 'terlambat'])
            ->count();
    }

    /**
     * Cek apakah member memiliki pinjaman terlambat yang belum diselesaikan.
     */
    public static function hasOverdueLoan(Member $member): bool
    {
        return $member->loans()->where('status', 'terlambat')->exists();
    }

    /**
     * Validasi kelayakan peminjam.
     *
     * @return array<string, mixed> ['ok' => bool, 'message' => string|null]
     */
    public static function eligibility(Member $member): array
    {
        if (!$member->is_active) {
            return ['ok' => false, 'message' => 'Anggota tidak aktif. Harap hubungi pustakawan.'];
        }

        if (self::hasOverdueLoan($member)) {
            return ['ok' => false, 'message' => 'Anda masih memiliki pinjaman terlambat. Selesaikan dulu ya!'];
        }

        $loaned = self::activeLoanCount($member);
        $quota = self::quotaFor($member);

        if ($loaned >= $quota) {
            return ['ok' => false, 'message' => "Kuota pinjaman penuh ({$loaned}/{$quota}). Kembalikan dulu sebagian buku pinjaman."];
        }

        $remaining = $quota - $loaned;

        return ['ok' => true, 'message' => null, 'remaining_quota' => $remaining];
    }
}
<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;

class CodeGenerator
{
    /**
     * Generate kode buku: PREFIX-TAHUN-URUTAN
     * Contoh: FIK-2026-0001
     */
    public static function generateBookCode(string $prefix): string
    {
        $year = date('Y');
        $lastBook = Book::where('book_code', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastBook) {
            $parts = explode('-', $lastBook->book_code);
            $sequence = (int) end($parts) + 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }

    /**
     * Generate kode eksemplar: BOOK_CODE-URUTAN
     * Contoh: FIK-2026-0001-01
     */
    public static function generateItemCode(string $bookCode, int $sequence): string
    {
        return sprintf('%s-%02d', $bookCode, $sequence);
    }

    /**
     * Generate kode anggota: TIPE_PREFIX-URUTAN
     * Contoh: S-2026001 (Siswa), G-2026001 (Guru)
     */
    public static function generateMemberCode(string $type): string
    {
        $prefixMap = [
            'siswa' => 'S',
            'guru' => 'G',
            'staf' => 'T',
        ];
        $prefix = $prefixMap[$type] ?? 'X';
        $year = date('Y');

        $lastMember = Member::where('member_code', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastMember) {
            $parts = explode('-', $lastMember->member_code);
            $sequence = (int) end($parts) + 1;
        }

        return sprintf('%s-%s%03d', $prefix, $year, $sequence);
    }

    /**
     * Generate kode peminjaman: L-TAHUN-URUTAN
     * Contoh: L-2026-00042
     */
    public static function generateLoanCode(): string
    {
        $year = date('Y');
        $lastLoan = Loan::where('loan_code', 'like', "L-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastLoan) {
            $parts = explode('-', $lastLoan->loan_code);
            $sequence = (int) end($parts) + 1;
        }

        return sprintf('L-%s-%05d', $year, $sequence);
    }
}

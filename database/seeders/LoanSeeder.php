<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Member;
use App\Models\User;
use App\Services\CodeGenerator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoanSeeder extends Seeder
{
    /**
     * Seed transaksi peminjaman riil (idempotent / additive):
     *  - total 10 status "berjalan"  (jatuh tempo belum lewat)
     *  - total 10 status "terlambat" (jatuh tempo sudah lewat)
     *  - total 10 status "selesai"   (sudah dikembalikan, sebagian dengan denda)
     *
     * Stok book_items & available_stock buku selalu disinkronkan.
     */
    public function run(): void
    {
        if (Member::count() === 0 || BookItem::count() === 0) {
            $this->command?->error('Jalankan MemberSeeder & BookSeeder terlebih dahulu.');

            return;
        }

        $targets = [
            'selesai' => 10,
            'berjalan' => 10,
            'terlambat' => 10,
        ];

        // Hitung kekurangan per status agar aman dijalankan berulang kali.
        foreach ($targets as $status => $target) {
            $targets[$status] = max(0, $target - Loan::where('status', $status)->count());
        }

        $remaining = array_sum($targets);
        if ($remaining === 0) {
            $this->command?->info('Kuota transaksi peminjaman sudah terpenuhi (10 berjalan, 10 terlambat, 10 selesai).');

            return;
        }

        $librarian = User::where('role', 'pustakawan')->first() ?? User::first();
        $affectedBookIds = [];
        $created = 0;

        DB::transaction(function () use ($librarian, &$affectedBookIds, &$created, $targets) {
            // Selesaikan dulu transaksi lama agar eksemplar kembali "tersedia",
            // baru buat transaksi aktif (berjalan & terlambat) dari eksemplar yang tersedia.
            foreach ($targets as $status => $count) {
                for ($i = 0; $i < $count; $i++) {
                    $member = $this->pickEligibleMember($status);
                    if (!$member) {
                        continue;
                    }

                    $itemCount = min($this->itemCountFor($member), $this->availableItems()->count());
                    $items = $this->availableItems()->take($itemCount)->get();

                    if ($items->isEmpty()) {
                        continue;
                    }

                    [$borrowDate, $dueDate] = $this->datesFor($status, $member);
                    $loan = Loan::create([
                        'loan_code' => CodeGenerator::generateLoanCode(),
                        'member_id' => $member->id,
                        'user_id' => $librarian->id,
                        'borrow_date' => $borrowDate->toDateString(),
                        'due_date' => $dueDate->toDateString(),
                        'status' => $status,
                    ]);

                    foreach ($items as $item) {
                        $this->createLoanItem($loan, $item, $status, $dueDate);
                        $affectedBookIds[$item->book_id] = true;
                    }

                    $created++;
                }
            }

            // Sinkronkan available_stock setiap buku yang terlibat.
            foreach (array_keys($affectedBookIds) as $bookId) {
                Book::withoutEvents(fn () => Book::where('id', $bookId)->update([
                    'available_stock' => BookItem::where('book_id', $bookId)->where('status', 'tersedia')->count(),
                ]));
            }
        });

        $this->command?->info("{$created} transaksi peminjaman baru berhasil dibuat.");
    }

    protected function availableItems()
    {
        return BookItem::where('status', 'tersedia')
            ->where('condition', 'baik')
            ->inRandomOrder()
            ->limit(2);
    }

    protected function itemCountFor(Member $member): int
    {
        return rand(1, $member->type === 'siswa' ? 2 : 3);
    }

    /**
     * Pilih anggota yang belum memenuhi kuota pinjaman aktif.
     */
    protected function pickEligibleMember(string $status): ?Member
    {
        $members = Member::where('is_active', true)->get()->shuffle();

        foreach ($members as $member) {
            if ($status === 'selesai') {
                return $member;
            }

            $active = Loan::where('member_id', $member->id)
                ->whereIn('status', ['berjalan', 'terlambat'])
                ->count();

            $quota = (int) setting("max_loan_{$member->type}", $member->type === 'siswa' ? 2 : 5);
            if ($active + 1 <= $quota) {
                return $member;
            }
        }

        return null;
    }

    protected function datesFor(string $status, Member $member): array
    {
        $duration = (int) setting("loan_days_{$member->type}", $member->type === 'siswa' ? 7 : 14);
        $today = Carbon::today();

        if ($status === 'selesai') {
            $borrow = $today->copy()->subDays(rand(25, 60));

            return [$borrow, $borrow->copy()->addDays($duration)];
        }

        if ($status === 'berjalan') {
            return [$today->copy()->subDays(rand(0, 2)), $today->copy()->addDays($duration)];
        }

        // Terlambat: jatuh tempo sudah lewat dari hari ini.
        $lateDays = rand(1, 20);

        return [
            $today->copy()->subDays($duration + $lateDays),
            $today->copy()->subDays($lateDays),
        ];
    }

    protected function createLoanItem(Loan $loan, BookItem $item, string $status, Carbon $dueDate): void
    {
        $returnDate = null;
        $fine = 0;

        if ($status === 'selesai') {
            // Sebagian transaksi selesai dikembalikan terlambat (dikenakan denda).
            $late = rand(1, 10) <= 4;

            if ($late) {
                $returnDate = $dueDate->copy()->addDays(rand(1, 10));
                if ($returnDate->gt(Carbon::today())) {
                    $returnDate = Carbon::today()->copy()->subDay();
                }
            } else {
                $returnDate = $dueDate->copy()->subDays(rand(0, 3));
                if ($returnDate->lt($loan->borrow_date)) {
                    $returnDate = $loan->borrow_date;
                }
            }

            if (setting('fine_enabled', true)) {
                $lateDays = max(0, (int) $dueDate->diffInDays($returnDate));
                if ($lateDays > 0) {
                    $maxDays = (int) setting('fine_max_days', 30);
                    $perDay = (int) setting('fine_per_day', 500);
                    $fine = min($lateDays, $maxDays) * $perDay;
                }
            }

            // Kembalikan eksemplar ke stok.
            $item->update(['status' => 'tersedia', 'condition' => 'baik']);
        } else {
            // Eksemplar dipinjam.
            $item->update(['status' => 'dipinjam']);
        }

        LoanItem::create([
            'loan_id' => $loan->id,
            'book_item_id' => $item->id,
            'status' => $status === 'selesai' ? 'dikembalikan' : 'dipinjam',
            'return_date' => $returnDate?->toDateString(),
            'fine_amount' => $fine,
        ]);
    }
}

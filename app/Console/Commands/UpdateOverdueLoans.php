<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Illuminate\Console\Command;

class UpdateOverdueLoans extends Command
{
    protected $signature = 'loans:update-overdue';

    protected $description = 'Mengubah status pinjaman berjalan menjadi terlambat yang melewati due_date';

    public function handle(): int
    {
        $updated = Loan::whereIn('status', ['berjalan'])
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => 'terlambat']);

        $this->info("{$updated} pinjaman ditandai sebagai terlambat.");

        return self::SUCCESS;
    }
}
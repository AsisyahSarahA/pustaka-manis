<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Category;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\VisitorLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    /**
     * Parse & tentukan periode waktu: Mingguan, Bulanan, Tahunan, atau Kustom.
     */
    public function resolvePeriod(Request $request): array
    {
        $periodType = $request->query('period_type', 'monthly');
        $year = (int) $request->query('year', Carbon::now()->year);
        $month = (int) $request->query('month', Carbon::now()->month);
        $week = (int) $request->query('week', 1);

        if ($periodType === 'yearly') {
            $start = Carbon::createFromDate($year, 1, 1)->startOfYear()->toDateString();
            $end = Carbon::createFromDate($year, 12, 31)->endOfYear()->toDateString();
            $periodLabel = "Tahun {$year}";
            $periodTitleSuffix = "Tahunan ({$year})";
        } elseif ($periodType === 'weekly') {
            $monthStart = Carbon::createFromDate($year, $month, 1);
            $daysInMonth = $monthStart->daysInMonth;
            
            $startDay = max(1, min(($week - 1) * 7 + 1, $daysInMonth));
            $endDay = min($startDay + 6, $daysInMonth);
            
            $start = Carbon::createFromDate($year, $month, $startDay)->toDateString();
            $end = Carbon::createFromDate($year, $month, $endDay)->toDateString();
            $monthName = $monthStart->translatedFormat('F Y');
            $periodLabel = "Minggu ke-{$week} ({$startDay}-{$endDay} {$monthName})";
            $periodTitleSuffix = "Mingguan (Minggu {$week}, {$monthName})";
        } elseif ($periodType === 'custom') {
            $startInput = $request->query('start');
            $endInput = $request->query('end');

            $start = $startInput && strtotime($startInput)
                ? Carbon::parse($startInput)->toDateString()
                : Carbon::now()->startOfMonth()->toDateString();
            $end = $endInput && strtotime($endInput)
                ? Carbon::parse($endInput)->toDateString()
                : Carbon::now()->toDateString();

            if (Carbon::parse($end)->lt(Carbon::parse($start))) {
                [$start, $end] = [$end, $start];
            }

            $periodLabel = Carbon::parse($start)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($end)->translatedFormat('d M Y');
            $periodTitleSuffix = "Periode {$periodLabel}";
        } else {
            // Default: 'monthly'
            $periodType = 'monthly';
            $monthStart = Carbon::createFromDate($year, $month, 1);
            $start = $monthStart->copy()->startOfMonth()->toDateString();
            $end = $monthStart->copy()->endOfMonth()->toDateString();
            $periodLabel = $monthStart->translatedFormat('F Y');
            $periodTitleSuffix = "Bulanan ({$periodLabel})";
        }

        return [
            'period_type' => $periodType,
            'year' => $year,
            'month' => $month,
            'week' => $week,
            'start' => $start,
            'end' => $end,
            'period_label' => $periodLabel,
            'period_title_suffix' => $periodTitleSuffix,
            'month_name' => $periodLabel,
        ];
    }

    /**
     * Ambil data sirkulasi berkala (mingguan/bulanan/tahunan/kustom) lengkap dengan ringkasan eksekutif.
     */
    public function getCirculationData(array $period): array
    {
        $start = $period['start'];
        $end = $period['end'];

        $loans = Loan::with(['member', 'items.bookItem.book'])
            ->whereBetween('borrow_date', [$start, $end])
            ->orderByDesc('borrow_date')
            ->get();

        $returnedCount = LoanItem::where('status', 'dikembalikan')
            ->whereBetween('return_date', [$start, $end])
            ->count();

        $visitorCount = VisitorLog::whereBetween('visit_date', [$start, $end])->count();

        $totalFineAmount = LoanItem::where('fine_amount', '>', 0)
            ->whereBetween('return_date', [$start, $end])
            ->sum('fine_amount');

        $totalBorrowedItems = $loans->sum(fn ($l) => $l->items->count());

        $overdueLoansCount = Loan::whereIn('status', ['berjalan', 'terlambat'])
            ->where('due_date', '<', Carbon::now()->toDateString())
            ->whereBetween('borrow_date', [$start, $end])
            ->count();

        $lostItemsCount = LoanItem::where('status', 'hilang')
            ->whereBetween('updated_at', [$start, $end])
            ->count();

        $damagedItemsCount = LoanItem::where('status', 'dikembalikan')
            ->whereBetween('return_date', [$start, $end])
            ->whereHas('bookItem', fn ($q) => $q->where('condition', 'rusak'))
            ->count();

        return [
            ...$period,
            'loans' => $loans,
            'returns_count' => $returnedCount,
            'visitors_count' => $visitorCount,
            'total_fine_amount' => $totalFineAmount,
            'total_borrowed_items' => $totalBorrowedItems,
            'overdue_count' => $overdueLoansCount,
            'lost_count' => $lostItemsCount,
            'damaged_count' => $damagedItemsCount,
            'report_title' => 'Laporan Sirkulasi ' . $period['period_title_suffix'],
            'generated_at' => Carbon::now(),
        ];
    }

    /**
     * Backward-compatible alias untuk getCirculationData bulanan.
     */
    public function getMonthlyCirculationData(int $month, int $year): array
    {
        $monthStart = Carbon::createFromDate($year, $month, 1);
        $period = [
            'period_type' => 'monthly',
            'year' => $year,
            'month' => $month,
            'week' => 1,
            'start' => $monthStart->copy()->startOfMonth()->toDateString(),
            'end' => $monthStart->copy()->endOfMonth()->toDateString(),
            'period_label' => $monthStart->translatedFormat('F Y'),
            'period_title_suffix' => "Bulanan ({$monthStart->translatedFormat('F Y')})",
            'month_name' => $monthStart->translatedFormat('F Y'),
        ];

        return $this->getCirculationData($period);
    }

    /**
     * Ambil laporan inventaris buku dengan kategori & status.
     */
    public function getInventoryData(?int $categoryId = null, ?string $status = null): array
    {
        $query = Book::with(['category', 'items'])->withCount('items');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $books = $query->orderBy('title')->get();

        if ($status) {
            $books = $books->filter(function ($book) use ($status) {
                return $book->items->where('status', $status)->isNotEmpty();
            })->values();
        }

        $totalTitles = $books->count();
        $totalItems = $books->sum('items_count');
        $availableItems = $books->sum(fn ($b) => $b->items->where('status', 'tersedia')->count());
        $borrowedItems = $books->sum(fn ($b) => $b->items->where('status', 'dipinjam')->count());
        $damagedItems = $books->sum(fn ($b) => $b->items->where('status', 'rusak')->count());
        $lostItems = $books->sum(fn ($b) => $b->items->where('status', 'hilang')->count());

        return [
            'books' => $books,
            'category_id' => $categoryId,
            'status' => $status,
            'categories' => Category::orderBy('name')->get(),
            'total_titles' => $totalTitles,
            'total_items' => $totalItems,
            'available_items' => $availableItems,
            'borrowed_items' => $borrowedItems,
            'damaged_items' => $damagedItems,
            'lost_items' => $lostItems,
            'report_title' => 'Laporan Inventaris Koleksi Buku',
            'generated_at' => Carbon::now(),
        ];
    }

    /**
     * Ambil laporan transaksi peminjaman / keterlambatan dengan filter periode & kelas.
     */
    public function getLoanReportData(Request $request, array $period, bool $overdueOnly = false): array
    {
        $start = $period['start'];
        $end = $period['end'];
        $hasCustomOrPeriod = $request->filled('period_type') || $request->filled('start') || $request->filled('year');

        $query = Loan::with(['member'])->withCount('items');

        if ($overdueOnly && !$hasCustomOrPeriod) {
            // Jika tab keterlambatan dibuka pertama kali tanpa filter, tampilkan semua keterlambatan aktif
            $query->where(function ($q) {
                $q->where('status', 'terlambat')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'berjalan')
                         ->whereDate('due_date', '<', Carbon::now()->toDateString());
                  });
            });
            $periodLabel = 'Semua Keterlambatan Aktif';
        } else {
            $query->whereBetween('borrow_date', [$start, $end]);
            $periodLabel = $period['period_label'];

            if ($overdueOnly) {
                $query->where(function ($q) {
                    $q->where('status', 'terlambat')
                      ->orWhere(function ($q2) {
                          $q2->where('status', 'berjalan')
                             ->whereDate('due_date', '<', Carbon::now()->toDateString());
                      });
                });
            }
        }

        if ($request->filled('class')) {
            $query->whereHas('member', fn ($q) => $q->where('department_class', 'like', '%' . $request->input('class') . '%'));
        }

        $loans = $query->orderByDesc('borrow_date')->get();

        return [
            ...$period,
            'loans' => $loans,
            'class' => $request->input('class', ''),
            'overdue' => $overdueOnly,
            'period_label' => $periodLabel,
            'report_title' => $overdueOnly
                ? 'Laporan Keterlambatan ' . $period['period_title_suffix']
                : 'Laporan Peminjaman ' . $period['period_title_suffix'],
        ];
    }

    /**
     * Ambil laporan kunjungan dengan filter periode & tipe pengunjung.
     */
    public function getVisitorReportData(Request $request, array $period): array
    {
        $start = $period['start'];
        $end = $period['end'];

        $query = VisitorLog::with('member')
            ->whereBetween('visit_date', [$start, $end]);

        if ($request->filled('visitor_type')) {
            $query->where('visitor_type', $request->input('visitor_type'));
        }

        $visitors = $query->orderByDesc('visit_date')->get();

        return [
            ...$period,
            'visitors' => $visitors,
            'visitor_type' => $request->input('visitor_type', ''),
            'report_title' => 'Laporan Kunjungan ' . $period['period_title_suffix'],
        ];
    }

    /**
     * Generate formal PDF for reports.
     */
    public function generatePdf(string $type, array $data)
    {
        $pdf = Pdf::loadView('reports.pdf.' . $type, $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
            ]);

        return $pdf;
    }
}

<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\VisitorLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    /**
     * Get monthly circulation data including executive summary metrics.
     */
    public function getMonthlyCirculationData(int $month, int $year): array
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $end = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

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
            'month' => $month,
            'year' => $year,
            'month_name' => Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y'),
            'loans' => $loans,
            'returns_count' => $returnedCount,
            'visitors_count' => $visitorCount,
            'total_fine_amount' => $totalFineAmount,
            'total_borrowed_items' => $totalBorrowedItems,
            'overdue_count' => $overdueLoansCount,
            'lost_count' => $lostItemsCount,
            'damaged_count' => $damagedItemsCount,
            'report_title' => 'Laporan Sirkulasi Bulanan Perpustakaan',
            'generated_at' => Carbon::now(),
        ];
    }

    /**
     * Get inventory report data with category breakdown.
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
            'categories' => \App\Models\Category::orderBy('name')->get(),
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
     * Generate formal PDF for monthly circulation or inventory.
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

    /**
     * Ambil laporan transaksi peminjaman dengan filter rentang tanggal & kelas.
     * N+1 dihindari dengan with(['member']) + withCount('items').
     */
    public function getLoanReportData(Request $request, bool $overdueOnly = false): array
    {
        [$start, $end] = $this->parseDateRange($request);
        $hasRange = $request->filled('start') || $request->filled('end');

        $query = Loan::with(['member'])->withCount('items');

        if ($overdueOnly && !$hasRange) {
            // Default laporan keterlambatan: tampilkan SEMUA yang sedang terlambat,
            // tanpa batas tanggal pinjam (agar transaksi lama tetap terlihat).
            $start = '';
            $end = '';
        } else {
            $query->whereBetween('borrow_date', [$start, $end]);
        }

        if ($request->filled('class')) {
            $query->whereHas('member', fn ($q) => $q->where('department_class', 'like', '%' . $request->input('class') . '%'));
        }

        if ($overdueOnly) {
            // Transaksi yang benar-benar telat: status terlambat ATAU masih berjalan tapi lewat jatuh tempo.
            $query->where(function ($q) {
                $q->where('status', 'terlambat')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'berjalan')
                         ->whereDate('due_date', '<', Carbon::now()->toDateString());
                  });
            });
        }

        $loans = $query->orderByDesc('borrow_date')->get();

        return [
            'loans' => $loans,
            'start' => $start,
            'end' => $end,
            'class' => $request->input('class', ''),
            'overdue' => $overdueOnly,
        ];
    }

    /**
     * Ambil laporan kunjungan (buku tamu) dengan filter rentang tanggal & tipe pengunjung.
     */
    public function getVisitorReportData(Request $request): array
    {
        [$start, $end] = $this->parseDateRange($request);

        $query = VisitorLog::with('member')
            ->whereBetween('visit_date', [$start, $end]);

        if ($request->filled('visitor_type')) {
            $query->where('visitor_type', $request->input('visitor_type'));
        }

        $visitors = $query->orderByDesc('visit_date')->get();

        return [
            'visitors' => $visitors,
            'start' => $start,
            'end' => $end,
            'visitor_type' => $request->input('visitor_type', ''),
        ];
    }

    /**
     * Parse rentang tanggal dari query string, dengan fallback yang aman:
     * kosong/tidak valid → awal bulan s.d. hari ini. Tanggal dibalik bila start > end.
     */
    protected function parseDateRange(Request $request): array
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $start = $start && strtotime($start) ? Carbon::parse($start)->toDateString() : Carbon::now()->startOfMonth()->toDateString();
        $end = $end && strtotime($end) ? Carbon::parse($end)->toDateString() : Carbon::now()->toDateString();

        if (Carbon::parse($end)->lt(Carbon::parse($start))) {
            [$start, $end] = [$end, $start];
        }

        return [$start, $end];
    }
}

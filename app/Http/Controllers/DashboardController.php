<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\VisitorLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalBooks = Book::count();
        $availableBooks = BookItem::where('status', 'tersedia')->count();
        $borrowedBooks = BookItem::where('status', 'dipinjam')->count();
        $overdueCount = Loan::where('status', 'terlambat')->count();
        $todayVisitors = VisitorLog::whereDate('visit_date', today())->count();

        // Tren peminjaman 7 hari terakhir
        $loanTrend = $this->loanTrend();

        // Tren kunjungan 7 hari terakhir
        $visitTrend = $this->visitTrend();

        // Top kategori dipinjam
        $topCategories = $this->topCategories();

        return view('dashboard.index', compact(
            'totalBooks',
            'availableBooks',
            'borrowedBooks',
            'overdueCount',
            'todayVisitors',
            'loanTrend',
            'visitTrend',
            'topCategories'
        ));
    }

    public function chartData(): JsonResponse
    {
        return response()->json([
            'loanTrend' => $this->loanTrend(),
            'visitTrend' => $this->visitTrend(),
            'topCategories' => $this->topCategories(),
        ]);
    }

    private function loanTrend(): array
    {
        $days = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i)->toDateString());
        $rows = Loan::where('borrow_date', '>=', $days->first())
            ->selectRaw('borrow_date, count(*) as total')
            ->groupBy('borrow_date')
            ->pluck('total', 'borrow_date');

        $data = $days->map(fn ($d) => (int) ($rows[$d] ?? 0))->values();
        $labels = $days->map(fn ($d) => Carbon::parse($d)->translatedFormat('d M'))->values();
        $total = $data->sum();
        $avg = round($total / 7, 1);

        $maxVal = $data->max();
        $maxIdx = $data->search($maxVal);
        $peakDay = $maxVal > 0 && $maxIdx !== false ? $labels[$maxIdx] . " ({$maxVal} buku)" : '-';

        return [
            'labels' => $labels,
            'data' => $data,
            'total_7_days' => $total,
            'avg_per_day' => $avg,
            'peak_day' => $peakDay,
        ];
    }

    private function visitTrend(): array
    {
        $days = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i)->toDateString());
        $rows = VisitorLog::where('visit_date', '>=', $days->first())
            ->selectRaw('visit_date, count(*) as total')
            ->groupBy('visit_date')
            ->pluck('total', 'visit_date');

        $data = $days->map(fn ($d) => (int) ($rows[$d] ?? 0))->values();
        $labels = $days->map(fn ($d) => Carbon::parse($d)->translatedFormat('d M'))->values();
        $total = $data->sum();
        $avg = round($total / 7, 1);

        $maxVal = $data->max();
        $maxIdx = $data->search($maxVal);
        $peakDay = $maxVal > 0 && $maxIdx !== false ? $labels[$maxIdx] . " ({$maxVal} pengunjung)" : '-';

        return [
            'labels' => $labels,
            'data' => $data,
            'total_7_days' => $total,
            'avg_per_day' => $avg,
            'peak_day' => $peakDay,
        ];
    }

    private function topCategories(): array
    {
        $rows = LoanItem::query()
            ->join('book_items', 'loan_items.book_item_id', '=', 'book_items.id')
            ->join('books', 'book_items.book_id', '=', 'books.id')
            ->join('categories', 'books.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category, count(*) as total')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $colors = ['#97DDE9', '#CFEBFF', '#FBBF24', '#F87171', '#A78BFA'];
        $totalAll = $rows->sum('total');

        $items = $rows->map(function ($row, $index) use ($colors, $totalAll) {
            $count = (int) $row->total;
            $percentage = $totalAll > 0 ? round(($count / $totalAll) * 100, 1) : 0;
            return [
                'name' => $row->category,
                'total' => $count,
                'percentage' => $percentage,
                'color' => $colors[$index % count($colors)],
            ];
        });

        return [
            'labels' => $rows->pluck('category')->values(),
            'data' => $rows->pluck('total')->map(fn ($v) => (int) $v)->values(),
            'total_all' => $totalAll,
            'items' => $items,
        ];
    }
}
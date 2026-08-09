<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = [];

        // 1. Trigger Stok Habis (available_stock == 0)
        $outOfStockBooks = Book::where('available_stock', 0)
            ->limit(5)
            ->get(['id', 'title', 'book_code']);

        foreach ($outOfStockBooks as $book) {
            $notifications[] = [
                'id' => 'stock_' . $book->id,
                'type' => 'out_of_stock',
                'title' => '❌ Stok Buku Habis',
                'message' => "Stok eksemplar '{$book->title}' ({$book->book_code}) telah habis (0).",
                'url' => route('books.show', $book->id),
                'icon' => '❌',
                'severity' => 'danger',
            ];
        }

        // 2. Trigger Overdue (Terlambat)
        $overdueCount = Loan::whereIn('status', ['berjalan', 'terlambat'])
            ->where('due_date', '<', Carbon::now()->toDateString())
            ->count();

        if ($overdueCount > 0) {
            $notifications[] = [
                'id' => 'overdue_summary',
                'type' => 'overdue',
                'title' => '⚠️ Transaksi Terlambat',
                'message' => "Terdapat {$overdueCount} transaksi peminjaman buku yang melewati jatuh tempo.",
                'url' => route('loans.return'),
                'icon' => '⚠️',
                'severity' => 'warning',
            ];
        }

        return response()->json([
            'total_unread' => count($notifications),
            'notifications' => $notifications,
        ]);
    }
}

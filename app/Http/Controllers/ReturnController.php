<?php

namespace App\Http\Controllers;

use App\Models\BookItem;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function index(Request $request): View
    {
        $member = null;
        $loans = collect();

        $memberId = $request->query('member_id');

        if ($memberId) {
            $member = Member::find($memberId);

            if ($member) {
                $loans = $member->loans()
                    ->with(['items' => fn ($q) => $q->where('status', 'dipinjam'), 'items.bookItem.book'])
                    ->whereIn('status', ['berjalan', 'terlambat'])
                    ->orderByDesc('id')
                    ->get();
            }
        }

        return view('loans.return', compact('member', 'loans'));
    }

    /**
     * Endpoint AJAX: scan barcode buku / kartu anggota pada layar pengembalian.
     */
    public function searchMember(Request $request): JsonResponse
    {
        $code = trim((string) $request->query('code', ''));

        if ($code === '') {
            return response()->json(['found' => false, 'message' => 'Silakan scan barcode buku atau ketik identitas.'], 422);
        }

        // 1. Cek apakah yang discan adalah kartu anggota / nomor identitas
        $member = Member::where('identity_number', $code)
            ->orWhere('member_code', $code)
            ->first();

        // 2. Cek apakah yang discan adalah barcode / kode eksemplar buku yang sedang dipinjam
        if (!$member) {
            $bookItem = BookItem::where('barcode', $code)
                ->orWhere('item_code', $code)
                ->first();

            if ($bookItem) {
                $activeLoanItem = LoanItem::where('book_item_id', $bookItem->id)
                    ->where('status', 'dipinjam')
                    ->with('loan.member')
                    ->latest('id')
                    ->first();

                if ($activeLoanItem && $activeLoanItem->loan && $activeLoanItem->loan->member) {
                    $member = $activeLoanItem->loan->member;
                } else {
                    return response()->json([
                        'found' => false,
                        'message' => "Buku '{$bookItem->item_code}' ({$bookItem->book?->title}) tidak sedang dalam status dipinjam.",
                    ], 404);
                }
            }
        }

        // 3. Cek apakah yang discan adalah kode transaksi pinjam
        if (!$member) {
            $loan = Loan::where('loan_code', $code)->with('member')->first();
            if ($loan && $loan->member) {
                $member = $loan->member;
            }
        }

        if (!$member) {
            return response()->json(['found' => false, 'message' => "Kode '{$code}' tidak ditemukan sebagai buku dipinjam atau kartu anggota."], 404);
        }

        $activeCount = $member->loans()
            ->whereIn('status', ['berjalan', 'terlambat'])
            ->count();

        return response()->json([
            'found' => true,
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'member_code' => $member->member_code,
                'type' => $member->type_label,
                'department_class' => $member->department_class,
                'active_loans' => $activeCount,
            ],
            'redirect' => route('loans.return', ['member_id' => $member->id]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'returned' => ['required', 'array', 'min:1'],
            'returned.*' => ['required', 'integer'],
            'condition' => ['required', 'array'],
            'condition.*' => ['required', 'in:baik,rusak,hilang'],
        ]);

        $member = Member::findOrFail($validated['member_id']);

        DB::transaction(function () use ($member, $validated) {
            $loanIds = $validated['returned'];

            // Ambil loan items yang diproses
            $loanItems = LoanItem::whereIn('id', $loanIds)
                ->whereHas('loan', fn ($q) => $q->where('member_id', $member->id))
                ->with(['loan', 'bookItem', 'bookItem.book'])
                ->lockForUpdate()
                ->get();

            $affectedLoanIds = $loanItems->pluck('loan_id')->unique();

            $dueDate = null;

            foreach ($loanItems as $loanItem) {
                $condition = $validated['condition'][$loanItem->id];
                $returnDate = now()->toDateString();

                // Hitung denda berdasar tanggal jatuh tempo loan
                $due = Carbon::parse($loanItem->loan->due_date)->startOfDay();
                $returnDateParsed = Carbon::parse($returnDate)->startOfDay();

                $deltaDays = 0;
                if ($returnDateParsed->gt($due)) {
                    $deltaDays = (int) abs($due->diffInDays($returnDateParsed));
                }

                $fine = 0;
                if (setting('fine_enabled', true) && $deltaDays > 0) {
                    $fineMaxDays = (int) setting('fine_max_days', 30);
                    $finePerDay = (int) setting('fine_per_day', 500);
                    $fine = min($deltaDays, $fineMaxDays) * $finePerDay;
                }

                $loanItem->update([
                    'status' => $condition === 'hilang' ? 'hilang' : 'dikembalikan',
                    'return_date' => $returnDate,
                    'fine_amount' => $fine,
                ]);

                $bookItem = $loanItem->bookItem;
                $bookItem->update([
                    'condition' => $condition,
                    'status' => $condition === 'baik' ? 'tersedia' : 'perbaikan',
                ]);

                $book = $bookItem->book;
                $book->forceFill([
                    'available_stock' => $book->items()->where('status', 'tersedia')->count(),
                ])->save();
            }

            // Update status setiap loan yang terpengaruh
            foreach (Loan::whereIn('id', $affectedLoanIds)->lockForUpdate()->get() as $loan) {
                $remainingBorrowed = $loan->items()->where('status', 'dipinjam')->count();

                if ($remainingBorrowed === 0) {
                    $loan->update([
                        'status' => 'selesai',
                        'return_date' => now()->toDateString(),
                    ]);
                } elseif ($dueDate !== null && Carbon::parse($loan->due_date)->isPast()) {
                    $loan->update(['status' => 'terlambat']);
                }
            }
        });

        return redirect()->route('loans.return', ['member_id' => $member->id])
            ->with('toast', ['type' => 'success', 'message' => 'Pengembalian berhasil diproses.']);
    }
}
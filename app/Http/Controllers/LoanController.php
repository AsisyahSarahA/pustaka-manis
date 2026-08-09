<?php

namespace App\Http\Controllers;

use App\Models\BookItem;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Member;
use App\Services\CodeGenerator;
use App\Services\LoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoanController extends Controller
{
    use \App\Http\Controllers\Concerns\HandlesLiveTables;

    public function index(Request $request): View
    {
        $query = Loan::with(['member', 'user'])->withCount('items')->orderByDesc('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('member_code', 'like', "%{$search}%")
                    ->orWhere('identity_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->paginate(15)->withQueryString();

        if ($this->isLiveTable($request)) {
            return view('loans.partials.results', compact('loans'));
        }

        return view('loans.index', compact('loans'));
    }

    public function borrow(): View
    {
        return view('loans.borrow');
    }

    /**
     * Endpoint AJAX: scan kartu anggota → detail member + kelayakan.
     */
    public function searchMember(Request $request): JsonResponse
    {
        $code = trim((string) $request->query('code', ''));
        $type = $request->query('type');

        $query = Member::query();
        if ($type) {
            $query->where('type', $type);
        }

        $member = $query->where(function ($q) use ($code) {
            $q->where('identity_number', $code)
              ->orWhere('member_code', $code);
        })->first();

        if (!$member) {
            return response()->json([
                'found' => false,
                'message' => 'Kode tidak ditemukan. Pastikan kartu anggota valid.',
            ], 404);
        }

        $eligibility = LoanService::eligibility($member);

        return response()->json([
            'found' => true,
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'member_code' => $member->member_code,
                'type' => $member->type_label,
                'department_class' => $member->department_class,
                'is_active' => $member->is_active,
                'active_loans' => LoanService::activeLoanCount($member),
                'quota' => LoanService::quotaFor($member),
                'remaining_quota' => $eligibility['remaining_quota'] ?? 0,
                'days_per_loan' => LoanService::durationFor($member),
            ],
            'eligibility' => $eligibility,
        ]);
    }

    /**
     * Endpoint AJAX: Scan barcode/kode eksemplar -> data buku.
     */
    public function searchBook(Request $request): JsonResponse
    {
        $code = trim((string) $request->query('code', ''));

        if ($code === '') {
            return response()->json(['found' => false, 'message' => 'Kode kosong.'], 422);
        }

        $item = BookItem::with('book')
            ->where('barcode', $code)
            ->orWhere('item_code', $code)
            ->first();

        if (!$item) {
            return response()->json(['found' => false, 'message' => 'Kode tidak ditemukan.'] , 404);
        }

        if (!$item->isAvailable()) {
            $statusLabel = $item->status_label . ($item->condition !== 'baik' ? " ({$item->condition_label})" : '');
            return response()->json([
                'found' => false,
                'message' => "Buku ini sedang {$statusLabel}. Tidak bisa dipinjam saat ini.",
            ], 422);
        }

        return response()->json([
            'found' => true,
            'item' => [
                'id' => $item->id,
                'item_code' => $item->item_code,
                'barcode' => $item->barcode,
                'title' => $item->book->title,
                'author' => $item->book->author,
                'book_code' => $item->book->book_code,
                'category' => $item->book->category?->name,
            ],
        ]);
    }

    /**
     * Finalisasi transaksi peminjaman.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['required', 'integer', 'distinct'],
        ]);

        $member = Member::findOrFail($validated['member_id']);

        // Validasi kelayakan peminjam
        $eligibility = LoanService::eligibility($member);
        if (!$eligibility['ok']) {
            return redirect()->route('loans.borrow')
                ->withInput()
                ->with('toast', ['type' => 'error', 'message' => $eligibility['message']]);
        }

        // Batasi jumlah item per transaksi sesuai sisa kuota
        $allowed = $eligibility['remaining_quota'];
        if (count($validated['items']) > $allowed) {
            return redirect()->route('loans.borrow')
                ->withInput()
                ->with('toast', ['type' => 'warning', 'message' => "Kuota tersisa hanya {$allowed} buku. Kurangi jumlah keranjang."]);
        }

        $loan = null;

        DB::transaction(function () use (&$loan, $member, $validated) {
            // Re-validasi tiap item masih tersedia di dalam transaksi.
            $items = BookItem::whereIn('id', $validated['items'])->lockForUpdate()->get();

            foreach ($items as $item) {
                if (!$item->isAvailable()) {
                    throw new \RuntimeException("Buku '{$item->book->title}' sedang dipinjam atau rusak.");
                }
            }

            $loanCode = CodeGenerator::generateLoanCode();
            $borrowDate = now()->toDateString();
            $dueDate = now()->addDays(LoanService::durationFor($member))->toDateString();

            $loan = Loan::create([
                'loan_code' => $loanCode,
                'member_id' => $member->id,
                'user_id' => auth()->id(),
                'borrow_date' => $borrowDate,
                'due_date' => $dueDate,
                'status' => 'berjalan',
            ]);

            foreach ($items as $item) {
                LoanItem::create([
                    'loan_id' => $loan->id,
                    'book_item_id' => $item->id,
                    'status' => 'dipinjam',
                    'fine_amount' => 0,
                ]);

                // Update status eksemplar -> dipinjam
                $item->update(['status' => 'dipinjam']);

                // Kurangi available_stock buku
                $book = $item->book;
                $book->forceFill([
                    'available_stock' => max(0, $book->available_stock - 1),
                ])->save();
            }
        });

        return redirect()->route('loans.receipt', $loan)
            ->with('toast', ['type' => 'success', 'message' => "Peminjaman {$loan->loan_code} berhasil diproses! 📚"]);
    }

    /**
     * Tampilkan slip termal peminjaman.
     */
    public function receipt(Loan $loan): View
    {
        $loan->load(['member', 'items.bookItem.book']);

        return view('loans.receipt', compact('loan'));
    }
}
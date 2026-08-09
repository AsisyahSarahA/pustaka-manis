<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FineController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Fine::with(['member', 'loanItem.bookItem.book', 'user'])->orderByDesc('id');

        if ($search) {
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_code', 'like', "%{$search}%")
                  ->orWhere('identity_number', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $fines = $query->paginate(15)->withQueryString();

        $totalUnpaid = Fine::where('status', 'unpaid')->sum('amount');
        $totalPaidMonth = Fine::where('status', 'paid')
            ->whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');

        return view('fines.index', compact('fines', 'totalUnpaid', 'totalPaidMonth', 'search', 'status'));
    }

    /**
     * Process payment of fine.
     */
    public function pay(Request $request, Fine $fine): RedirectResponse
    {
        if ($fine->status === 'paid') {
            return redirect()->back()->with('toast', ['type' => 'info', 'message' => 'Denda ini sudah lunas.']);
        }

        DB::transaction(function () use ($fine) {
            $receiptNumber = 'INV-FIN-' . date('Ymd') . '-' . sprintf('%04d', $fine->id);
            
            $fine->update([
                'status' => 'paid',
                'payment_date' => now(),
                'user_id' => auth()->id(),
                'receipt_number' => $receiptNumber,
            ]);

            // Update associated loan item fine status if present
            if ($fine->loan_item_id && $fine->loanItem) {
                $fine->loanItem->update([
                    'fine_amount' => $fine->amount,
                ]);
            }
        });

        return redirect()->route('fines.receipt', $fine)
            ->with('toast', ['type' => 'success', 'message' => 'Pembayaran denda berhasil diproses! 💸']);
    }

    /**
     * Waive fine with notes.
     */
    public function waive(Request $request, Fine $fine): RedirectResponse
    {
        $request->validate([
            'notes' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($fine, $request) {
            $fine->update([
                'status' => 'waived',
                'user_id' => auth()->id(),
                'notes' => 'Dimaafkan: ' . $request->input('notes'),
            ]);
        });

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Denda berhasil dimaafkan.']);
    }

    /**
     * Display thermal / A4 printable receipt with Skeuomorphic "LUNAS" stamp effect.
     */
    public function receipt(Fine $fine): View
    {
        $fine->load(['member', 'loanItem.bookItem.book', 'user']);

        return view('fines.receipt', compact('fine'));
    }
}

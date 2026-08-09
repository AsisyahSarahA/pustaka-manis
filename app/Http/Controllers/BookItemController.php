<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookItemController extends Controller
{
    public function index(Book $book): View
    {
        $items = $book->items()->orderBy('item_code')->paginate(20);

        return view('books.items', compact('book', 'items'));
    }

    public function update(Request $request, BookItem $bookItem): RedirectResponse
    {
        $request->validate([
            'condition' => ['required', 'in:baik,rusak,hilang'],
            'status' => ['required', 'in:tersedia,dipinjam,perbaikan'],
        ]);

        $bookItem->update($request->only(['condition', 'status']));

        $book = $bookItem->book;
        $book->forceFill([
            'available_stock' => $book->items()->where('status', 'tersedia')->count(),
        ])->save();

        return redirect()->route('book-items.index', $book)
            ->with('toast', ['type' => 'success', 'message' => 'Status eksemplar diperbarui.']);
    }

    public function destroy(BookItem $bookItem): RedirectResponse
    {
        if ($bookItem->status === 'dipinjam') {
            return redirect()->route('book-items.index', $bookItem->book)
                ->with('toast', ['type' => 'error', 'message' => 'Eksemplar yang dipinjam tidak bisa dihapus.']);
        }

        $book = $bookItem->book;
        $bookItem->delete();

        $book->forceFill([
            'total_stock' => $book->items()->count(),
            'available_stock' => $book->items()->where('status', 'tersedia')->count(),
        ])->save();

        return redirect()->route('book-items.index', $book)
            ->with('toast', ['type' => 'success', 'message' => 'Eksemplar berhasil dihapus.']);
    }

    public function printLabels(Book $book): View
    {
        $items = $book->items()->orderBy('item_code')->get();

        return view('books.labels-print', compact('book', 'items'));
    }

    public function printSingleLabel(BookItem $bookItem): View
    {
        $book = $bookItem->book;
        $items = collect([$bookItem]);

        return view('books.labels-print', compact('book', 'items'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    /**
     * Endpoint pencarian global: buku, eksemplar, anggota.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['books' => [], 'items' => [], 'members' => []]);
        }

        $books = Book::with('category')
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('book_code', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(fn ($b) => [
                'label' => $b->title,
                'sub' => $b->book_code . ' · ' . ($b->category->name ?? '-'),
                'url' => route('books.show', $b),
            ]);

        $items = BookItem::with('book')
            ->where(function ($query) use ($q) {
                $query->where('item_code', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(fn ($i) => [
                'label' => $i->book->title,
                'sub' => 'Eksemplar: ' . $i->item_code . ' (' . $i->status_label . ')',
                'url' => route('book-items.index', $i->book),
            ]);

        $members = Member::where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('identity_number', 'like', "%{$q}%")
                    ->orWhere('member_code', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(fn ($m) => [
                'label' => $m->name,
                'sub' => $m->member_code . ' · ' . $m->type_label,
                'url' => route('members.show', $m),
            ]);

        return response()->json([
            'books' => $books,
            'items' => $items,
            'members' => $members,
        ]);
    }
}
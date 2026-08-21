<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Category;
use App\Services\CodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookController extends Controller
{
    use \App\Http\Controllers\Concerns\HandlesLiveTables;

    public function index(Request $request): View
    {
        $query = Book::with('category')->withCount('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('book_code', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $books = $query->orderBy('title')->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        if ($this->isLiveTable($request)) {
            return view('books.partials.results', compact('books'));
        }

        return view('books.index', compact('books', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('books.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['book_code'] = CodeGenerator::generateBookCode(
            Category::findOrFail($data['category_id'])->prefix
        );
        $data['total_stock'] = (int) $data['total_stock'];

        if ($request->hasFile('cover_image')) {
            File::ensureDirectoryExists(public_path('uploads/books'));
            $file = $request->file('cover_image');
            $filename = 'cover-' . now()->format('YmdHis') . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $filename);
            $data['cover_image'] = 'uploads/books/' . $filename;
        }

        Book::create($data);

        return redirect()->route('books.index')
            ->with('toast', ['type' => 'success', 'message' => 'Buku berhasil ditambahkan beserta eksemplarnya.']);
    }

    public function show(Book $book): View
    {
        $book->load(['category', 'items']);

        $loanHistory = \App\Models\LoanItem::with(['loan.member', 'bookItem'])
            ->whereIn('book_item_id', $book->items->pluck('id'))
            ->orderByDesc('id')
            ->get();

        return view('books.show', compact('book', 'loanHistory'));
    }

    public function edit(Book $book): View
    {
        $categories = Category::orderBy('name')->get();

        return view('books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['total_stock'] = (int) $data['total_stock'];

        if ($request->hasFile('cover_image')) {
            File::ensureDirectoryExists(public_path('uploads/books'));
            $file = $request->file('cover_image');
            $filename = 'cover-' . now()->format('YmdHis') . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $filename);

            if ($book->cover_image && File::exists(public_path($book->cover_image))) {
                File::delete(public_path($book->cover_image));
            }

            $data['cover_image'] = 'uploads/books/' . $filename;
        }

        DB::transaction(function () use ($book, $data) {
            $book->update($data);

            $existingCount = $book->items()->count();
            $currentAvailable = $book->items()->where('status', 'tersedia')->count();

            if ($data['total_stock'] > $existingCount) {
                for ($i = $existingCount + 1; $i <= $data['total_stock']; $i++) {
                    $itemCode = CodeGenerator::generateItemCode($book->book_code, $i);
                    $book->items()->create([
                        'item_code' => $itemCode,
                        'barcode' => $itemCode,
                        'condition' => 'baik',
                        'status' => 'tersedia',
                    ]);
                }
                $book->forceFill([
                    'available_stock' => $currentAvailable + ($data['total_stock'] - $existingCount),
                    'total_stock' => $data['total_stock'],
                ])->save();
            } elseif ($data['total_stock'] < $existingCount) {
                $toDelete = $existingCount - $data['total_stock'];
                $book->items()
                    ->where('status', 'tersedia')
                    ->latest('id')
                    ->take($toDelete)
                    ->delete();

                $book->forceFill([
                    'available_stock' => max(0, $book->items()->where('status', 'tersedia')->count()),
                    'total_stock' => $data['total_stock'],
                ])->save();
            }
        });

        return redirect()->route('books.show', $book)
            ->with('toast', ['type' => 'success', 'message' => 'Buku berhasil diperbarui.']);
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->items()->where('status', 'dipinjam')->exists()) {
            return redirect()->route('books.index')
                ->with('toast', ['type' => 'error', 'message' => 'Tidak bisa menghapus buku yang masih memiliki eksemplar dipinjam.']);
        }

        if ($book->cover_image && File::exists(public_path($book->cover_image))) {
            File::delete(public_path($book->cover_image));
        }

        $book->delete();

        return redirect()->route('books.index')
            ->with('toast', ['type' => 'success', 'message' => 'Buku berhasil dihapus.']);
    }

    public function importForm(): View
    {
        return view('books.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ]);

        $file = $request->file('file');

        $rows = collect();

        if (in_array($file->getClientOriginalExtension(), ['xlsx', 'xls'])) {
            if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return redirect()->route('books.index')
                    ->with('toast', ['type' => 'error', 'message' => 'Package maatwebsite/excel belum terinstal.']);
            }

            $rows = \Maatwebsite\Excel\Facades\Excel::toCollection(null, $file)->first() ?? collect();
        } else {
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle);
            while (($data = fgetcsv($handle)) !== false) {
                $rows->push(array_combine($header, $data));
            }
            fclose($handle);
        }

        if ($rows->isEmpty()) {
            return redirect()->route('books.index')
                ->with('toast', ['type' => 'error', 'message' => 'File impor kosong atau format tidak sesuai.']);
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $row = collect($row)->map(fn ($v) => trim((string) $v));

            try {
                $category = Category::where('name', $row['kategori'] ?? '')->orWhere('slug', $row['kategori'] ?? '')->first();

                if (!$category) {
                    throw new \Exception('Kategori "' . ($row['kategori'] ?? '') . '" tidak ditemukan');
                }

                if (empty($row['judul']) || empty($row['penulis']) || empty($row['penerbit'])) {
                    throw new \Exception('Field wajib (judul/penulis/penerbit) kosong');
                }

                $book = Book::create([
                    'book_code' => CodeGenerator::generateBookCode($category->prefix),
                    'title' => $row['judul'],
                    'category_id' => $category->id,
                    'author' => $row['penulis'],
                    'publisher' => $row['penerbit'],
                    'publication_year' => $row['tahun_terbit'] ?? date('Y'),
                    'rack_location' => $row['lokasi_rak'] ?? null,
                    'total_stock' => (int) ($row['jumlah_eksemplar'] ?? 1),
                    'available_stock' => 0,
                ]);

                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = 'Baris ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }

        $message = "Impor selesai: {$success} buku berhasil, {$failed} gagal.";
        if (count($errors) > 0) {
            $message .= ' ' . implode(' | ', array_slice($errors, 0, 5));
        }

        return redirect()->route('books.index')
            ->with('toast', ['type' => $failed > 0 ? 'warning' : 'success', 'message' => $message]);
    }

    public function downloadTemplate()
    {
        $headers = ['judul', 'penulis', 'penerbit', 'tahun_terbit', 'kategori', 'jumlah_eksemplar', 'lokasi_rak'];
        $csv = implode(',', $headers) . "\n";
        $csv .= '"Laskar Pelangi","Andrea Hirata","Bentang Pustaka","2005","Fiksi","5","Rak A-1"' . "\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-buku.csv"',
        ]);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['required', 'string', 'max:255'],
            'publication_year' => ['required', 'string', 'size:4'],
            'rack_location' => ['nullable', 'string', 'max:50'],
            'total_stock' => ['required', 'integer', 'min:1', 'max:999'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use \App\Http\Controllers\Concerns\HandlesLiveTables;

    public function index(Request $request): View
    {
        $query = Category::withCount('books');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('name')->paginate(15)->withQueryString();

        if ($this->isLiveTable($request)) {
            return view('categories.partials.results', compact('categories'));
        }

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.form', ['category' => new Category]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        Category::create($data);

        return redirect()->route('categories.index')
            ->with('toast', ['type' => 'success', 'message' => 'Kategori berhasil ditambahkan.']);
    }

    public function edit(Category $category): View
    {
        return view('categories.form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validateData($request, $category->id);

        $category->update($data);

        return redirect()->route('categories.index')
            ->with('toast', ['type' => 'success', 'message' => 'Kategori berhasil diperbarui.']);
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->books()->exists()) {
            return redirect()->route('categories.index')
                ->with('toast', ['type' => 'error', 'message' => 'Kategori tidak bisa dihapus karena masih memiliki buku.']);
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('toast', ['type' => 'success', 'message' => 'Kategori berhasil dihapus.']);
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'unique:categories,slug,' . $ignoreId],
            'prefix' => ['required', 'string', 'max:3', 'alpha'],
            'description' => ['nullable', 'string'],
        ]);
    }
}

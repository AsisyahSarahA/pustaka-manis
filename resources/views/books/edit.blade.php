<x-layouts.app>
    @section('page_title', 'Edit Buku')

    <div class="mx-auto max-w-3xl">
        <div class="glass-panel rounded-4xl p-6">
            <div class="mb-5 flex items-center gap-3">
                <x-badge variant="azure">{{ $book->book_code }}</x-badge>
                <span class="text-sm text-pearl/50">{{ $book->items_count ?? $book->items()->count() }} eksemplar</span>
            </div>

            <form method="POST" action="{{ route('books.update', $book) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-input
                    name="title"
                    label="Judul Buku"
                    :value="old('title', $book->title)"
                    :error="$errors->first('title')"
                    required
                />

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input
                        name="author"
                        label="Penulis"
                        :value="old('author', $book->author)"
                        :error="$errors->first('author')"
                        required
                    />
                    <x-input
                        name="publisher"
                        label="Penerbit"
                        :value="old('publisher', $book->publisher)"
                        :error="$errors->first('publisher')"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div>
                        <label for="category_id" class="mb-2 block text-sm font-medium text-pearl/80">Kategori</label>
                        <select name="category_id" id="category_id" class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $book->category_id) == $category->id)>{{ $category->name }} ({{ $category->prefix }})</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-danger-red">{{ $message }}</p>
                        @enderror
                    </div>
                    <x-input
                        name="publication_year"
                        label="Tahun Terbit"
                        :value="old('publication_year', $book->publication_year)"
                        :error="$errors->first('publication_year')"
                        maxlength="4"
                        required
                    />
                    <x-input
                        name="rack_location"
                        label="Lokasi Rak"
                        :value="old('rack_location', $book->rack_location)"
                        :error="$errors->first('rack_location')"
                    />
                </div>

                <x-input
                    type="number"
                    name="total_stock"
                    label="Total Stok (Eksemplar)"
                    :value="old('total_stock', $book->total_stock)"
                    :error="$errors->first('total_stock')"
                    min="1"
                    max="999"
                    required
                />

                <div class="rounded-2xl border border-azure-glow/20 bg-azure-soft/5 p-4 text-sm text-pearl/60">
                    💡 Menambah stok akan membuat eksemplar baru. Mengurangi stok hanya menghapus eksemplar yang masih berstatus 'tersedia'.
                </div>

                <div class="flex items-center justify-end gap-3">
                    <x-button href="{{ route('books.show', $book) }}" variant="secondary">Batal</x-button>
                    <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

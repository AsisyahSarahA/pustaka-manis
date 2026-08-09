<x-layouts.app>
    @section('page_title', 'Tambah Buku')

    <div class="mx-auto max-w-3xl">
        <div class="glass-panel rounded-4xl p-6">
            <form method="POST" action="{{ route('books.store') }}" class="space-y-5">
                @csrf

                <x-input
                    name="title"
                    label="Judul Buku"
                    placeholder="mis. Laskar Pelangi"
                    :value="old('title')"
                    :error="$errors->first('title')"
                    required
                />

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input
                        name="author"
                        label="Penulis"
                        placeholder="mis. Andrea Hirata"
                        :value="old('author')"
                        :error="$errors->first('author')"
                        required
                    />
                    <x-input
                        name="publisher"
                        label="Penerbit"
                        placeholder="mis. Bentang Pustaka"
                        :value="old('publisher')"
                        :error="$errors->first('publisher')"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div>
                        <label for="category_id" class="mb-2 block text-sm font-medium text-pearl/80">Kategori</label>
                        <select name="category_id" id="category_id" class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm" required>
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }} ({{ $category->prefix }})</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-danger-red">{{ $message }}</p>
                        @enderror
                    </div>
                    <x-input
                        name="publication_year"
                        label="Tahun Terbit"
                        placeholder="mis. 2005"
                        :value="old('publication_year')"
                        :error="$errors->first('publication_year')"
                        maxlength="4"
                        required
                    />
                    <x-input
                        name="rack_location"
                        label="Lokasi Rak"
                        placeholder="mis. Rak A-1"
                        :value="old('rack_location')"
                        :error="$errors->first('rack_location')"
                    />
                </div>

                <x-input
                    type="number"
                    name="total_stock"
                    label="Total Stok (Eksemplar)"
                    placeholder="mis. 5"
                    :value="old('total_stock', 1)"
                    :error="$errors->first('total_stock')"
                    min="1"
                    max="999"
                    required
                />

                <div class="rounded-2xl border border-azure-glow/20 bg-azure-soft/5 p-4 text-sm text-pearl/60">
                    💡 Sistem otomatis membuat kode buku (<code class="font-mono text-azure-soft">PREFIX-2026-0001</code>) dan
                    eksemplar (<code class="font-mono text-azure-soft">PREFIX-2026-0001-01</code>) untuk setiap stok.
                </div>

                <div class="flex items-center justify-end gap-3">
                    <x-button href="{{ route('books.index') }}" variant="secondary">Batal</x-button>
                    <x-button type="submit" variant="primary">Simpan Buku</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

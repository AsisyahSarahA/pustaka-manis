<x-layouts.app>
    @section('page_title', 'Edit Buku')

    <div class="mx-auto max-w-3xl" x-data="{ previewUrl: '{{ $book->cover_url }}' }">
        <div class="glass-panel p-6">
            <div class="mb-5 flex items-center justify-between border-b-2 border-black pb-3">
                <div class="flex items-center gap-3">
                    <x-badge variant="azure">{{ $book->book_code }}</x-badge>
                    <span class="font-mono text-xs font-bold text-black/70">{{ $book->items_count ?? $book->items()->count() }} EKSEMPLAR</span>
                </div>
                <span class="font-mono text-xs font-bold text-black/50">EDIT MODE</span>
            </div>

            <form method="POST" action="{{ route('books.update', $book) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Input Foto Cover & Live Preview --}}
                <div class="border-3 border-black bg-brutal-input p-5 shadow-brutal-sm">
                    <label class="mb-3 block text-xs font-black uppercase tracking-wider text-black font-heading">
                        📸 Foto Cover Buku
                    </label>

                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        {{-- Preview Box --}}
                        <div class="relative flex h-44 w-32 shrink-0 items-center justify-center border-3 border-black bg-white overflow-hidden shadow-brutal">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" alt="Preview Cover" class="h-full w-full object-cover" />
                            </template>
                            <template x-if="!previewUrl">
                                <div class="text-center p-2 text-black/50">
                                    <span class="text-3xl block">🖼️</span>
                                    <span class="text-[10px] font-black uppercase tracking-wider mt-1 block">Belum Ada Foto</span>
                                </div>
                            </template>
                        </div>

                        {{-- Upload Controls --}}
                        <div class="flex-1 space-y-2 text-center sm:text-left">
                            <label class="btn-brutal inline-flex items-center gap-2 border-2 border-black bg-brutal-yellow px-5 py-2.5 text-xs font-black uppercase tracking-wider text-black cursor-pointer shadow-brutal-sm">
                                <span>📁 Ganti Foto Cover</span>
                                <input
                                    type="file"
                                    name="cover_image"
                                    accept="image/png,image/jpeg,image/jpg,image/webp"
                                    class="hidden"
                                    @change="if ($event.target.files.length) { previewUrl = URL.createObjectURL($event.target.files[0]); }"
                                />
                            </label>
                            <p class="text-xs font-medium text-black/70 mt-1">
                                Format: JPG, PNG, WEBP. Maksimal 2 MB.
                            </p>
                            @error('cover_image')
                                <p class="text-xs font-bold text-brutal-pink mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

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
                        <label for="category_id" class="mb-2 block text-xs font-black uppercase text-black">Kategori</label>
                        <select name="category_id" id="category_id" class="input-debossed w-full px-4 py-3 text-sm font-bold" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $book->category_id) == $category->id)>{{ $category->name }} ({{ $category->prefix }})</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs font-bold text-brutal-pink">{{ $message }}</p>
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

                <div class="border-2 border-black bg-brutal-yellow/20 p-4 text-xs font-bold text-black shadow-brutal-sm">
                    💡 Menambah stok akan membuat eksemplar baru. Mengurangi stok hanya menghapus eksemplar yang berstatus 'tersedia'.
                </div>

                <div class="flex items-center justify-end gap-3 pt-3">
                    <x-button href="{{ route('books.show', $book) }}" variant="secondary" class="shadow-brutal">Batal</x-button>
                    <x-button type="submit" variant="primary" class="shadow-brutal">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

<x-layouts.app>
    @section('page_title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')

    <div class="mx-auto max-w-2xl">
        <div class="glass-panel rounded-4xl p-6">
            <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}" class="space-y-5">
                @csrf
                @if ($category->exists)
                    @method('PUT')
                @endif

                <x-input
                    name="name"
                    label="Nama Kategori"
                    placeholder="mis. Fiksi"
                    :value="old('name', $category->name)"
                    :error="$errors->first('name')"
                    required
                />

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input
                        name="slug"
                        label="Slug"
                        placeholder="mis. fiksi"
                        :value="old('slug', $category->slug)"
                        :error="$errors->first('slug')"
                        required
                    />
                    <x-input
                        name="prefix"
                        label="Awalan Kode (maks 3 huruf)"
                        placeholder="mis. FIK"
                        :value="old('prefix', $category->prefix)"
                        :error="$errors->first('prefix')"
                        required
                        maxlength="3"
                    />
                </div>

                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-pearl/80">Deskripsi</label>
                    <textarea
                        name="description"
                        id="description"
                        rows="3"
                        placeholder="Deskripsi singkat kategori (opsional)"
                        class="input-debossed w-full rounded-2xl border-0 px-4 py-3 text-sm"
                    >{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-danger-red">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3">
                    <x-button href="{{ route('categories.index') }}" variant="secondary">Batal</x-button>
                    <x-button type="submit" variant="primary">Simpan Kategori</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

<x-layouts.app>
    @section('page_title', 'Katalog Buku')

    <div x-data="liveTable('{{ route('books.index') }}')" class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-pearl">Katalog Buku</h2>
                <p class="mt-1 text-sm text-pearl/50">Kelola inventaris perpustakaan beserta eksemplarnya.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <x-button href="{{ route('books.create') }}" variant="primary">
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Buku
                </x-button>
                <x-button href="{{ route('books.template') }}" variant="secondary"><x-icon name="download" class="h-4 w-4" /> Template CSV</x-button>
                <x-button href="{{ route('books.import') }}" variant="secondary"><x-icon name="upload" class="h-4 w-4" /> Import</x-button>
            </div>
        </div>

        <form data-live-form method="GET" class="glass-panel rounded-4xl p-4" @submit.prevent="reload()">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_200px_auto]">
                <x-input
                    name="search"
                    placeholder="Cari judul, kode, atau penulis..."
                    :value="request('search')"
                    @input.debounce.300ms="reload()"
                />
                <select name="category_id" @change="reload()" class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-button type="submit" variant="primary"><x-icon name="search" class="h-4 w-4" /> Cari</x-button>
            </div>
        </form>

        {{-- Loading indicator --}}
        <div x-show="loading" x-cloak class="flex items-center justify-center gap-2 py-4 text-sm text-pearl/50">
            <span class="h-4 w-4 animate-spin rounded-full border-2 border-azure-soft border-t-transparent"></span>
            Memuat...
        </div>

        <div data-live-results>
            @include('books.partials.results', ['books' => $books])
        </div>
    </div>
</x-layouts.app>
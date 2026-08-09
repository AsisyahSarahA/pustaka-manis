<x-layouts.app>
    @section('page_title', 'Kategori Buku')

    <div x-data="liveTable('{{ route('categories.index') }}')" class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-pearl">Manajemen Kategori</h2>
                <p class="mt-1 text-sm text-pearl/50">Atur kategori dan awalan kode untuk katalog buku.</p>
            </div>
            <x-button href="{{ route('categories.create') }}" variant="primary">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Kategori
            </x-button>
        </div>

        <form data-live-form method="GET" class="glass-panel rounded-4xl p-4" @submit.prevent="reload()">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_auto]">
                <x-input
                    name="search"
                    placeholder="Cari nama atau slug kategori..."
                    :value="request('search')"
                    @input.debounce.300ms="reload()"
                />
                <x-button type="submit" variant="primary"><x-icon name="search" class="h-4 w-4" /> Cari</x-button>
            </div>
        </form>

        <div x-show="loading" x-cloak class="flex items-center justify-center gap-2 py-4 text-sm text-pearl/50">
            <span class="h-4 w-4 animate-spin rounded-full border-2 border-azure-soft border-t-transparent"></span>
            Memuat...
        </div>

        <div data-live-results>
            @include('categories.partials.results', ['categories' => $categories])
        </div>
    </div>
</x-layouts.app>
<x-layouts.app>
    @section('page_title', 'Anggota Perpustakaan')

    <div x-data="liveTable('{{ route('members.index') }}')" class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-pearl">Anggota Perpustakaan</h2>
                <p class="mt-1 text-sm text-pearl/50">Kelola data siswa, guru, staf, dan pengunjung.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <x-button href="{{ route('members.cards.print', request()->query()) }}" target="_blank" variant="secondary">
                    🪪 Cetak Semua Kartu
                </x-button>
                <x-button href="{{ route('members.create') }}" variant="primary">
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Anggota
                </x-button>
            </div>
        </div>

        <form data-live-form method="GET" class="glass-panel rounded-4xl p-4" @submit.prevent="reload()">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_200px_auto]">
                <x-input
                    name="search"
                    placeholder="Cari nama, NIS/NIP, atau kode anggota..."
                    :value="request('search')"
                    @input.debounce.300ms="reload()"
                />
                <select name="type" @change="reload()" class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm">
                    <option value="">Semua Tipe</option>
                    @foreach (['siswa', 'guru', 'staf', 'eksternal'] as $type)
                        <option value="{{ $type }}" @selected(request('type') == $type)>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
                <x-button type="submit" variant="primary"><x-icon name="search" class="h-4 w-4" /> Cari</x-button>
            </div>
        </form>

        <div x-show="loading" x-cloak class="flex items-center justify-center gap-2 py-4 text-sm text-pearl/50">
            <span class="h-4 w-4 animate-spin rounded-full border-2 border-azure-soft border-t-transparent"></span>
            Memuat...
        </div>

        <div data-live-results>
            @include('members.partials.results', ['members' => $members])
        </div>
    </div>
</x-layouts.app>

<x-layouts.app>
    @section('page_title', 'Riwayat Peminjaman')

    <div x-data="liveTable('{{ route('loans.index') }}')" class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-pearl">Riwayat Peminjaman</h2>
                <p class="mt-1 text-sm text-pearl/50">Pantau seluruh transaksi sirkulasi perpustakaan.</p>
            </div>
            <div class="flex gap-3">
                <x-button href="{{ route('loans.borrow') }}" variant="primary"><x-icon name="book-open" class="h-4 w-4" /> Pinjam Buku</x-button>
                <x-button href="{{ route('loans.return') }}" variant="secondary"><x-icon name="inbox" class="h-4 w-4" /> Pengembalian</x-button>
            </div>
        </div>

        <form data-live-form method="GET" class="glass-panel rounded-4xl p-4" @submit.prevent="reload()">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_200px_auto]">
                <x-input name="search" placeholder="Cari nama anggota, kode transaksi..." :value="request('search')" @input.debounce.300ms="reload()" />
                <select name="status" @change="reload()" class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm">
                    <option value="">Semua Status</option>
                    @foreach (['berjalan', 'terlambat', 'selesai', 'dibatalkan'] as $status)
                        <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
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
            @include('loans.partials.results', ['loans' => $loans])
        </div>
    </div>
</x-layouts.app>
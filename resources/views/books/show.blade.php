<x-layouts.app>
    @section('page_title', $book->title)

    <div class="space-y-6" x-data="{ activeTab: 'items' }">
        <div class="glass-panel p-6 flex flex-col sm:flex-row gap-6 items-start justify-between">
            <div class="flex flex-col sm:flex-row gap-5 items-start">
                <div class="h-48 w-36 shrink-0 border-3 border-black bg-white overflow-hidden shadow-brutal flex items-center justify-center">
                    @if($book->cover_url)
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="h-full w-full object-cover" />
                    @else
                        <div class="text-center p-3 text-black/50">
                            <span class="text-3xl block">📖</span>
                            <span class="text-[10px] font-black uppercase tracking-wider mt-1 block">No Cover</span>
                        </div>
                    @endif
                </div>

                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-badge variant="azure">{{ $book->book_code }}</x-badge>
                        <x-badge variant="neutral">{{ $book->category->name ?? '-' }}</x-badge>
                        @if ($book->is_active)
                            <x-badge variant="azure" dot>Aktif</x-badge>
                        @else
                            <x-badge variant="red" dot>Nonaktif</x-badge>
                        @endif
                    </div>
                    <h2 class="mt-3 text-2xl font-black uppercase tracking-tight text-black font-heading">{{ $book->title }}</h2>
                    <p class="mt-1 text-sm font-medium text-black/70">
                        oleh <span class="text-black font-bold">{{ $book->author }}</span> · {{ $book->publisher }} · {{ $book->publication_year }}
                        @if ($book->rack_location) · Rak <span class="border border-black bg-brutal-yellow px-1.5 py-0.5 font-mono font-black text-black">{{ $book->rack_location }}</span> @endif
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 shrink-0">
                <x-button href="{{ route('books.labels', $book) }}" target="_blank" variant="amber" class="shadow-brutal">
                    🖨️ CETAK LABEL
                </x-button>
                <x-button href="{{ route('book-items.index', $book) }}" variant="secondary" class="shadow-brutal">EKSEMPLAR</x-button>
                <x-button href="{{ route('books.edit', $book) }}" variant="primary" class="shadow-brutal">EDIT BUKU</x-button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="glass-panel p-5">
                <p class="text-xs font-black uppercase tracking-wider text-black/60">Total Stok</p>
                <p class="mt-1 font-mono text-3xl font-black text-black">{{ $book->total_stock }}</p>
            </div>
            <div class="glass-panel p-5">
                <p class="text-xs font-black uppercase tracking-wider text-black/60">Tersedia</p>
                <p class="mt-1 font-mono text-3xl font-black text-black">{{ $book->available_stock }}</p>
            </div>
            <div class="glass-panel p-5">
                <p class="text-xs font-black uppercase tracking-wider text-black/60">Dipinjam</p>
                <p class="mt-1 font-mono text-3xl font-black text-black">{{ $book->items()->where('status', 'dipinjam')->count() }}</p>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="flex gap-2 border-b border-white/10 pb-2">
            <button
                @click="activeTab = 'items'"
                :class="activeTab === 'items' ? 'bg-azure-soft/20 text-azure-soft border-azure-soft/40 font-bold' : 'text-pearl/60 hover:text-pearl'"
                class="rounded-full border border-transparent px-5 py-2 text-sm font-semibold transition"
            >
                📋 Eksemplar Buku ({{ $book->items->count() }})
            </button>
            <button
                @click="activeTab = 'history'"
                :class="activeTab === 'history' ? 'bg-azure-soft/20 text-azure-soft border-azure-soft/40 font-bold' : 'text-pearl/60 hover:text-pearl'"
                class="rounded-full border border-transparent px-5 py-2 text-sm font-semibold transition"
            >
                📜 Riwayat Peminjaman ({{ count($loanHistory) }})
            </button>
        </div>

        {{-- Tab 1: Daftar Eksemplar --}}
        <div x-show="activeTab === 'items'" class="glass-panel overflow-hidden rounded-4xl">
            <x-table :headers="['Kode Eksemplar', 'Barcode', 'Kondisi', 'Status', 'Aksi Label']">
                @forelse ($book->items as $item)
                    <tr class="transition-colors hover:bg-white/5">
                        <td class="px-4 py-3 font-mono text-sm text-azure-soft">{{ $item->item_code }}</td>
                        <td class="px-4 py-3 font-mono text-sm text-pearl/60">{{ $item->barcode }}</td>
                        <td class="px-4 py-3">
                            <x-badge :variant="$item->condition === 'baik' ? 'azure' : ($item->condition === 'rusak' ? 'amber' : 'red')">{{ $item->condition_label }}</x-badge>
                        </td>
                        <td class="px-4 py-3">
                            <x-badge :variant="$item->status === 'tersedia' ? 'success' : ($item->status === 'dipinjam' ? 'blue' : 'neutral')">{{ $item->status_label }}</x-badge>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('book-items.label', $item) }}" target="_blank" class="text-xs font-semibold text-azure-soft hover:underline">
                                🖨️ Label
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-pearl/40">Belum ada eksemplar.</td>
                    </tr>
                @endforelse
            </x-table>
        </div>

        {{-- Tab 2: Riwayat Peminjaman (Traceability Matrix) --}}
        <div x-show="activeTab === 'history'" x-cloak class="glass-panel overflow-hidden rounded-4xl">
            <x-table :headers="['No. Pinjam', 'Peminjam / Anggota', 'Eksemplar', 'Tgl Pinjam', 'Tgl Kembali', 'Kondisi Akhir', 'Surat Kehilangan']">
                @forelse ($loanHistory as $history)
                    <tr class="transition-colors hover:bg-white/5">
                        <td class="px-4 py-3 font-mono text-xs font-bold text-azure-soft">
                            {{ $history->loan->loan_code ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-pearl">{{ $history->loan->member->name ?? '-' }}</div>
                            <div class="text-xs text-pearl/50">{{ $history->loan->member->member_code ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-pearl/70">
                            {{ $history->bookItem->item_code ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-xs">
                            {{ $history->loan ? \Carbon\Carbon::parse($history->loan->borrow_date)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-xs">
                            {{ $history->return_date ? \Carbon\Carbon::parse($history->return_date)->format('d/m/Y') : 'Belum Kembali' }}
                        </td>
                        <td class="px-4 py-3 text-xs">
                            @if($history->status === 'hilang')
                                <span class="rounded-pill bg-danger-red/20 px-2.5 py-0.5 font-semibold text-danger-red">Hilang</span>
                            @elseif($history->condition_after === 'rusak')
                                <span class="rounded-pill bg-amber-500/20 px-2.5 py-0.5 font-semibold text-amber-300">Rusak</span>
                            @else
                                <span class="rounded-pill bg-success-green/20 px-2.5 py-0.5 font-semibold text-success-green">Baik</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs">
                            @if($history->status === 'hilang')
                                <a href="{{ route('reports.export.skk', $history->id) }}" class="inline-flex items-center gap-1 rounded-pill bg-red-500/20 px-2.5 py-1 text-xs font-bold text-red-300 hover:bg-red-500/30">
                                    📄 Word SKK
                                </a>
                            @else
                                <span class="text-pearl/30">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-pearl/40">Belum ada riwayat peminjaman untuk koleksi buku ini.</td>
                    </tr>
                @endforelse
            </x-table>
        </div>
    </div>
</x-layouts.app>

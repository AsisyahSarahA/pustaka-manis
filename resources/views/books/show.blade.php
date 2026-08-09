<x-layouts.app>
    @section('page_title', $book->title)

    <div class="space-y-6" x-data="{ activeTab: 'items' }">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-badge variant="azure">{{ $book->book_code }}</x-badge>
                    <x-badge variant="neutral">{{ $book->category->name ?? '-' }}</x-badge>
                    @if ($book->is_active)
                        <x-badge variant="success" dot>Aktif</x-badge>
                    @else
                        <x-badge variant="red" dot>Nonaktif</x-badge>
                    @endif
                </div>
                <h2 class="mt-3 text-2xl font-bold text-pearl">{{ $book->title }}</h2>
                <p class="mt-1 text-sm text-pearl/50">
                    oleh {{ $book->author }} · {{ $book->publisher }} · {{ $book->publication_year }}
                    @if ($book->rack_location) · Rak {{ $book->rack_location }} @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-button href="{{ route('books.labels', $book) }}" target="_blank" variant="secondary" class="border-azure-soft/30 text-azure-soft hover:bg-azure-soft/10">
                    🖨️ Cetak Label Barcode
                </x-button>
                <x-button href="{{ route('book-items.index', $book) }}" variant="secondary">Eksemplar</x-button>
                <x-button href="{{ route('books.edit', $book) }}" variant="primary">Edit Buku</x-button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="glass-panel rounded-4xl p-5">
                <p class="text-sm text-pearl/50">Total Stok</p>
                <p class="mt-1 text-3xl font-bold text-pearl">{{ $book->total_stock }}</p>
            </div>
            <div class="glass-panel rounded-4xl p-5">
                <p class="text-sm text-pearl/50">Tersedia</p>
                <p class="mt-1 text-3xl font-bold text-azure-soft">{{ $book->available_stock }}</p>
            </div>
            <div class="glass-panel rounded-4xl p-5">
                <p class="text-sm text-pearl/50">Dipinjam</p>
                <p class="mt-1 text-3xl font-bold text-amber-warm">{{ $book->items()->where('status', 'dipinjam')->count() }}</p>
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

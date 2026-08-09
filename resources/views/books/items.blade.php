<x-layouts.app>
    @section('page_title', 'Eksemplar — ' . $book->title)

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <x-badge variant="azure">{{ $book->book_code }}</x-badge>
                </div>
                <h2 class="mt-2 text-xl font-bold text-pearl">{{ $book->title }}</h2>
                <p class="mt-1 text-sm text-pearl/50">
                    {{ $book->available_stock }}/{{ $book->total_stock }} tersedia
                </p>
            </div>
            <x-button href="{{ route('books.show', $book) }}" variant="secondary"><x-icon name="arrow-left" class="h-4 w-4" /> Kembali</x-button>
        </div>

        <div class="glass-panel overflow-hidden rounded-4xl">
            <x-table :headers="['Kode Eksemplar', 'Barcode', 'Kondisi', 'Status', 'Aksi']">
                @forelse ($items as $item)
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
                            <form method="POST" action="{{ route('book-items.update', $item) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select name="status" class="input-debossed rounded-pill border-0 px-3 py-1.5 text-xs" @disabled($item->status === 'dipinjam')>
                                    @foreach (['tersedia', 'perbaikan'] as $s)
                                        <option value="{{ $s }}" @selected($item->status === $s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                                <select name="condition" class="input-debossed rounded-pill border-0 px-3 py-1.5 text-xs">
                                    @foreach (['baik', 'rusak', 'hilang'] as $c)
                                        <option value="{{ $c }}" @selected($item->condition === $c)>{{ ucfirst($c) }}</option>
                                    @endforeach
                                </select>
                                <x-button type="submit" variant="secondary" class="px-3 py-1.5 text-xs">Simpan</x-button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-pearl/40">Belum ada eksemplar untuk buku ini.</td>
                    </tr>
                @endforelse
            </x-table>
            <div class="px-4 py-3">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>

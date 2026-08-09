<div class="glass-panel overflow-hidden rounded-4xl">
    <div class="p-5">
        <h3 class="font-bold text-pearl">Inventaris Buku ({{ $books->count() }} judul)</h3>
        <p class="mt-1 text-xs text-pearl/40">Status eksemplar yang ditampilkan sesuai filter.</p>
    </div>
    <x-table :headers="['Kode', 'Judul', 'Kategori', 'Rak', 'Total', 'Tersedia', 'Dipinjam', 'Perbaikan']">
        @forelse ($books as $book)
            <tr class="transition-colors hover:bg-white/5">
                <td class="px-4 py-3 font-mono text-sm text-azure-soft">{{ $book->book_code }}</td>
                <td class="px-4 py-3 font-medium text-pearl">{{ $book->title }}</td>
                <td class="px-4 py-3 text-pearl/60">{{ $book->category->name ?? '-' }}</td>
                <td class="px-4 py-3 text-pearl/60">{{ $book->rack_location ?? '-' }}</td>
                <td class="px-4 py-3 text-pearl/60">{{ $book->items_count }}</td>
                <td class="px-4 py-3 text-success-green">{{ $book->items->where('status', 'tersedia')->count() }}</td>
                <td class="px-4 py-3 text-azure-soft">{{ $book->items->where('status', 'dipinjam')->count() }}</td>
                <td class="px-4 py-3 text-amber-warm">{{ $book->items->where('status', 'perbaikan')->count() }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-4 py-10 text-center text-pearl/40">Tidak ada data buku.</td>
            </tr>
        @endforelse
    </x-table>
</div>
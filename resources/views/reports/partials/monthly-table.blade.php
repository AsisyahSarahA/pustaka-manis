<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
        <div class="glass-panel rounded-4xl p-4 text-center">
            <p class="text-2xl font-bold text-pearl">{{ $totalBorrowed }}</p>
            <p class="text-xs text-pearl/50">Buku Dipinjam</p>
        </div>
        <div class="glass-panel rounded-4xl p-4 text-center">
            <p class="text-2xl font-bold text-success-green">{{ $returns }}</p>
            <p class="text-xs text-pearl/50">Pengembalian</p>
        </div>
        <div class="glass-panel rounded-4xl p-4 text-center">
            <p class="text-2xl font-bold text-azure-soft">{{ $visitors }}</p>
            <p class="text-xs text-pearl/50">Kunjungan</p>
        </div>
        <div class="glass-panel rounded-4xl p-4 text-center">
            <p class="text-2xl font-bold text-danger-red">{{ $overdue }}</p>
            <p class="text-xs text-pearl/50">Keterlambatan</p>
        </div>
        <div class="glass-panel rounded-4xl p-4 text-center">
            <p class="text-2xl font-bold text-amber-warm">Rp {{ number_format($totalFine, 0, ',', '.') }}</p>
            <p class="text-xs text-pearl/50">Total Denda</p>
        </div>
    </div>

    <div class="glass-panel overflow-hidden rounded-4xl">
        <div class="p-5">
            <h3 class="font-bold text-pearl">Rincian Peminjaman ({{ $loans->count() }})</h3>
        </div>
        <x-table :headers="['No. Transaksi', 'Peminjam', 'Kelas', 'Tanggal', 'Jatuh Tempo', 'Buku', 'Status']">
            @forelse ($loans as $loan)
                <tr class="transition-colors hover:bg-white/5">
                    <td class="px-4 py-3 font-mono text-sm text-azure-soft">{{ $loan->loan_code }}</td>
                    <td class="px-4 py-3 text-pearl">{{ $loan->member->name }}</td>
                    <td class="px-4 py-3 text-pearl/60">{{ $loan->member->department_class ?? '-' }}</td>
                    <td class="px-4 py-3 text-pearl/60">{{ $loan->borrow_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-pearl/60">{{ $loan->due_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-pearl/60">{{ $loan->items->count() }} buku</td>
                    <td class="px-4 py-3">
                        @if ($loan->status === 'selesai')
                            <x-badge variant="success" dot>Selesai</x-badge>
                        @elseif ($loan->status === 'terlambat' || $loan->is_late)
                            <x-badge variant="red" dot>Terlambat</x-badge>
                        @else
                            <x-badge variant="blue" dot>Berjalan</x-badge>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-pearl/40">Tidak ada data pada bulan ini.</td>
                </tr>
            @endforelse
        </x-table>
    </div>
</div>
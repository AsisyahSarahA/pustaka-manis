<div class="glass-panel overflow-hidden rounded-4xl">
    <x-table :headers="['No. Transaksi', 'Peminjam', 'Tanggal Pinjam', 'Jatuh Tempo', 'Buku', 'Status', '']">
        @forelse ($loans as $loan)
            <tr class="transition-colors hover:bg-white/5">
                <td class="px-4 py-3 font-mono text-sm text-azure-soft">{{ $loan->loan_code }}</td>
                <td class="px-4 py-3">
                    <p class="font-medium text-pearl">{{ $loan->member->name }}</p>
                    <p class="text-xs text-pearl/40">{{ $loan->member->member_code }}</p>
                </td>
                <td class="px-4 py-3 text-pearl/60">{{ $loan->borrow_date->format('d M Y') }}</td>
                <td class="px-4 py-3 text-pearl/60">
                    {{ $loan->due_date->format('d M Y') }}
                    @if ($loan->is_late)
                        <p class="text-xs text-danger-red">Terlambat {{ $loan->late_days }} hari</p>
                    @endif
                </td>
                <td class="px-4 py-3 text-pearl/60">{{ $loan->items_count }} buku</td>
                <td class="px-4 py-3">
                    @if ($loan->status === 'selesai')
                        <x-badge variant="success" dot>Selesai</x-badge>
                    @elseif ($loan->status === 'terlambat' || $loan->is_late)
                        <x-badge variant="red" dot>Terlambat</x-badge>
                    @elseif ($loan->status === 'berjalan')
                        <x-badge variant="blue" dot>Berjalan</x-badge>
                    @else
                        <x-badge variant="neutral">{{ $loan->status_label }}</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if ($loan->status !== 'selesai')
                        <a href="{{ route('loans.receipt', $loan) }}" target="_blank" class="rounded-xl p-2 text-pearl/60 transition hover:bg-white/10 hover:text-amber-warm" title="Cetak Slip">
                            <span class="block h-4 w-4"><x-icon name="printer" class="h-4 w-4" /></span>
                        </a>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-10 text-center text-pearl/40">
                    Belum ada transaksi peminjaman.
                </td>
            </tr>
        @endforelse
    </x-table>
    <div class="px-4 py-3">
        {{ $loans->links() }}
    </div>
</div>
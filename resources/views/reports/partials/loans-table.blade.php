<div class="glass-panel overflow-hidden rounded-4xl">
    <div class="flex items-center justify-between p-5">
        <h3 class="font-bold text-pearl">Daftar Peminjaman ({{ $loans->count() }})</h3>
        @if (!empty($start) && !empty($end))
            <span class="text-xs text-pearl/40">Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} — {{ \Carbon\Carbon::parse($end)->format('d M Y') }}</span>
        @else
            <span class="text-xs text-pearl/40">Periode: Semua waktu</span>
        @endif
    </div>
    <x-table :headers="['No. Transaksi', 'Peminjam', 'Kelas', 'Tanggal', 'Jatuh Tempo', 'Status']">
        @forelse ($loans as $loan)
            <tr class="transition-colors hover:bg-white/5">
                <td class="px-4 py-3 font-mono text-sm text-azure-soft">{{ $loan->loan_code }}</td>
                <td class="px-4 py-3 text-pearl">{{ $loan->member?->name ?? 'Anggota terhapus' }}</td>
                <td class="px-4 py-3 text-pearl/60">{{ $loan->member?->department_class ?? '-' }}</td>
                <td class="px-4 py-3 text-pearl/60">{{ optional($loan->borrow_date)->format('d M Y') }}</td>
                <td class="px-4 py-3 text-pearl/60">
                    {{ optional($loan->due_date)->format('d M Y') }}
                    @if (isset($overdue) && $overdue && $loan->is_late)
                        <span class="text-danger-red">(+{{ $loan->late_days }} hr)</span>
                    @endif
                </td>
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
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-10 text-center text-pearl/40">Tidak ada data pada periode ini.</td>
            </tr>
        @endforelse
    </x-table>
</div>
<x-layouts.app>
    @section('page_title', 'Kasir Denda')

    <div class="space-y-6" x-data="{ waiveModalOpen: false, activeFineId: null, waiveReason: '' }">
        {{-- Header & Stat Cards --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-pearl">Kasir Denda Perpustakaan</h2>
                <p class="mt-1 text-sm text-pearl/50">Kelola dan proses pelunasan atau pemutihan denda peminjaman anggota.</p>
            </div>
        </div>

        {{-- Stat Summary Grid --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div class="glass-panel rounded-3xl p-5 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/15 text-2xl text-amber-warm ring-1 ring-amber-500/30">
                    ⚠️
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-pearl/50">Total Menunggak (Unpaid)</p>
                    <p class="text-2xl font-bold text-amber-warm">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="glass-panel rounded-3xl p-5 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-success-green/15 text-2xl text-success-green ring-1 ring-success-green/30">
                    💰
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-pearl/50">Terkumpul Bulan Ini</p>
                    <p class="text-2xl font-bold text-success-green">Rp {{ number_format($totalPaidMonth, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Filter & Search Form --}}
        <form method="GET" action="{{ route('fines.index') }}" class="glass-panel rounded-4xl p-4">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <x-input name="search" label="Cari Anggota / No. Struk" :value="$search" placeholder="Nama, NISN, NIP, atau no. struk..." />
                <select name="status" class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm mt-6">
                    <option value="">Semua Status Denda</option>
                    <option value="unpaid" @selected($status === 'unpaid')>⚠️ Unpaid (Menunggak)</option>
                    <option value="paid" @selected($status === 'paid')>✅ Paid (Lunas)</option>
                    <option value="waived" @selected($status === 'waived')>🛡️ Waived (Dimaafkan)</option>
                </select>
                <div class="flex items-end">
                    <x-button type="submit" variant="primary" class="w-full">Cari Data Denda</x-button>
                </div>
            </div>
        </form>

        {{-- Fines Table --}}
        <div class="glass-panel overflow-hidden rounded-4xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-pearl">
                    <thead class="bg-navy-soft/60 text-xs font-semibold uppercase tracking-wider text-pearl/70 border-b border-white/10">
                        <tr>
                            <th class="px-6 py-4">No. Struk / Tgl</th>
                            <th class="px-6 py-4">Nama Anggota</th>
                            <th class="px-6 py-4">Judul Buku</th>
                            <th class="px-6 py-4 text-right">Nominal Denda</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($fines as $fine)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-6 py-4">
                                    <div class="font-mono text-xs font-bold text-azure-soft">{{ $fine->receipt_number ?? '#FIN-'.$fine->id }}</div>
                                    <div class="text-[11px] text-pearl/50">{{ \Carbon\Carbon::parse($fine->fine_date)->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-pearl">{{ $fine->member->name ?? '-' }}</div>
                                    <div class="text-xs text-pearl/50">{{ $fine->member->member_code ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-pearl/90">{{ $fine->loanItem->bookItem->book->title ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-amber-warm">
                                    Rp {{ number_format($fine->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($fine->status === 'paid')
                                        <span class="rounded-pill bg-success-green/20 px-3 py-1 text-xs font-semibold text-success-green">LUNAS</span>
                                    @elseif ($fine->status === 'waived')
                                        <span class="rounded-pill bg-azure-soft/20 px-3 py-1 text-xs font-semibold text-azure-soft">DIMAAFKAN</span>
                                    @else
                                        <span class="rounded-pill bg-danger-red/20 px-3 py-1 text-xs font-semibold text-danger-red">MENUNGGAK</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @if ($fine->status === 'unpaid')
                                        <form method="POST" action="{{ route('fines.pay', $fine) }}" class="inline">
                                            @csrf
                                            <x-button type="submit" variant="primary" class="py-1 px-3 text-xs">💵 Bayar</x-button>
                                        </form>
                                        <x-button type="button" variant="secondary" class="py-1 px-3 text-xs border-amber-500/30 text-amber-300 hover:bg-amber-500/10" @click="activeFineId = {{ $fine->id }}; waiveModalOpen = true">🛡️ Maafkan</x-button>
                                    @endif
                                    <a href="{{ route('fines.receipt', $fine) }}" target="_blank" class="inline-flex items-center rounded-pill bg-white/5 border border-white/10 px-3 py-1 text-xs font-semibold text-pearl hover:bg-white/10">🖨️ Struk</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-pearl/50">
                                    Tidak ada data denda ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-white/10">
                {{ $fines->links() }}
            </div>
        </div>

        {{-- Waive Modal --}}
        <div x-show="waiveModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-dark/80 backdrop-blur-sm p-4">
            <div class="w-full max-w-md glass-panel rounded-4xl p-6 space-y-4 border border-white/10 shadow-2xl">
                <h3 class="text-lg font-bold text-pearl">Pemutihan / Maafkan Denda</h3>
                <p class="text-xs text-pearl/60">Berikan alasan resmi mengapa denda ini dimaafkan / dibebaskan.</p>

                <form :action="'{{ url('/fines') }}/' + activeFineId + '/waive'" method="POST" class="space-y-4">
                    @csrf
                    <input type="text" name="notes" x-model="waiveReason" required placeholder="mis. Surat Keterangan Tidak Mampu / Kebijakan Kepala Sekolah" class="input-debossed w-full rounded-2xl border-0 px-4 py-3 text-sm text-pearl" />
                    <div class="flex justify-end gap-2">
                        <x-button type="button" variant="secondary" @click="waiveModalOpen = false">Batal</x-button>
                        <x-button type="submit" variant="primary">Simpan Pemutihan</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>

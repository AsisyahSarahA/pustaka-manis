<x-layouts.app>
    @section('page_title', $member->name)

    <div class="space-y-6" x-data="{ activeTab: 'active' }">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-4xl bg-azure-soft/15 text-2xl font-bold text-azure-soft ring-2 ring-azure-soft/30">
                    {{ strtoupper(substr($member->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-badge variant="azure">{{ $member->member_code }}</x-badge>
                        <x-badge :variant="$member->type === 'siswa' ? 'blue' : ($member->type === 'guru' ? 'azure' : 'neutral')">{{ $member->type_label }}</x-badge>
                        @if ($member->is_active)
                            <x-badge variant="success" dot>Aktif</x-badge>
                        @else
                            <x-badge variant="red" dot>Nonaktif</x-badge>
                        @endif
                    </div>
                    <h2 class="mt-2 text-2xl font-bold text-pearl">{{ $member->name }}</h2>
                    <p class="mt-1 text-sm text-pearl/50">
                        @if ($member->identity_number) No. Induk: {{ $member->identity_number }} · @endif
                        @if ($member->department_class) {{ $member->department_class }} · @endif
                        @if ($member->phone) Telp: {{ $member->phone }} @endif
                    </p>
                </div>
            </div>
            <div class="flex gap-3">
                <x-button href="{{ route('members.card', $member) }}" target="_blank" variant="secondary">🪪 Cetak Kartu</x-button>
                <x-button href="{{ route('members.edit', $member) }}" variant="primary">Edit Anggota</x-button>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="glass-panel rounded-4xl p-5">
                <p class="text-sm text-pearl/50">Total Pinjaman</p>
                <p class="mt-1 text-3xl font-bold text-pearl">{{ count($loans) }}</p>
            </div>
            <div class="glass-panel rounded-4xl p-5">
                <p class="text-sm text-pearl/50">Pinjaman Aktif</p>
                <p class="mt-1 text-3xl font-bold text-azure-soft">{{ count($activeLoans) }}</p>
            </div>
            <div class="glass-panel rounded-4xl p-5">
                <p class="text-sm text-pearl/50">Pinjaman Selesai</p>
                <p class="mt-1 text-3xl font-bold text-success-green">{{ count($pastLoans) }}</p>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="flex gap-2 border-b border-white/10 pb-2">
            <button
                @click="activeTab = 'active'"
                :class="activeTab === 'active' ? 'bg-azure-soft/20 text-azure-soft border-azure-soft/40 font-bold' : 'text-pearl/60 hover:text-pearl'"
                class="rounded-full border border-transparent px-5 py-2 text-sm font-semibold transition"
            >
                📌 Pinjaman Aktif ({{ count($activeLoans) }})
            </button>
            <button
                @click="activeTab = 'all'"
                :class="activeTab === 'all' ? 'bg-azure-soft/20 text-azure-soft border-azure-soft/40 font-bold' : 'text-pearl/60 hover:text-pearl'"
                class="rounded-full border border-transparent px-5 py-2 text-sm font-semibold transition"
            >
                📜 Riwayat Keseluruhan ({{ count($loans) }})
            </button>
        </div>

        {{-- Tab 1: Pinjaman Aktif (Red Highlight if Overdue) --}}
        <div x-show="activeTab === 'active'" class="glass-panel overflow-hidden rounded-4xl">
            <x-table :headers="['Kode Pinjam', 'Buku Dipinjam', 'Tgl Pinjam', 'Jatuh Tempo', 'Status Transaksi']">
                @forelse ($activeLoans as $loan)
                    @php
                        $isOverdue = \Carbon\Carbon::parse($loan->due_date)->isPast();
                    @endphp
                    <tr class="transition-colors {{ $isOverdue ? 'bg-danger-red/10 border-l-4 border-l-danger-red' : 'hover:bg-white/5' }}">
                        <td class="px-4 py-3 font-mono text-sm font-bold text-azure-soft">
                            {{ $loan->loan_code }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="space-y-1">
                                @foreach ($loan->items as $item)
                                    <div class="text-xs text-pearl">
                                        • <span class="font-semibold">{{ $item->bookItem->book->title ?? '-' }}</span> 
                                        <span class="font-mono text-pearl/50">[{{ $item->bookItem->item_code ?? '-' }}]</span>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-pearl/80">
                            {{ \Carbon\Carbon::parse($loan->borrow_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-xs font-bold {{ $isOverdue ? 'text-danger-red' : 'text-pearl/80' }}">
                            {{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y') }}
                            @if ($isOverdue)
                                <span class="block text-[10px] text-danger-red">⚠️ Terlambat</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($isOverdue)
                                <x-badge variant="red">Terlambat</x-badge>
                            @else
                                <x-badge variant="azure">Berjalan</x-badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-pearl/40">Tidak ada pinjaman aktif saat ini.</td>
                    </tr>
                @endforelse
            </x-table>
        </div>

        {{-- Tab 2: Riwayat Keseluruhan --}}
        <div x-show="activeTab === 'all'" x-cloak class="glass-panel overflow-hidden rounded-4xl">
            <x-table :headers="['Kode Pinjam', 'Daftar Buku', 'Tgl Pinjam', 'Jatuh Tempo', 'Tgl Kembali', 'Status']">
                @forelse ($loans as $loan)
                    <tr class="transition-colors hover:bg-white/5">
                        <td class="px-4 py-3 font-mono text-xs font-bold text-azure-soft">
                            {{ $loan->loan_code }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="space-y-1">
                                @foreach ($loan->items as $item)
                                    <div class="text-xs text-pearl">
                                        • {{ $item->bookItem->book->title ?? '-' }}
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-pearl/80">
                            {{ \Carbon\Carbon::parse($loan->borrow_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-xs text-pearl/80">
                            {{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-xs text-pearl/80">
                            {{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if ($loan->status === 'selesai')
                                <x-badge variant="success">Selesai</x-badge>
                            @elseif (\Carbon\Carbon::parse($loan->due_date)->isPast())
                                <x-badge variant="red">Terlambat</x-badge>
                            @else
                                <x-badge variant="azure">Berjalan</x-badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-pearl/40">Belum ada riwayat peminjaman.</td>
                    </tr>
                @endforelse
            </x-table>
        </div>
    </div>
</x-layouts.app>

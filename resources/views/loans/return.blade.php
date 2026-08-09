<x-layouts.app>
    @section('page_title', 'Pengembalian Buku')

    <div
        x-data="returnApp()"
        x-init="init()"
        class="mx-auto max-w-4xl space-y-6"
    >
        <div class="glass-panel rounded-4xl p-6">
            <h2 class="mb-4 text-lg font-bold text-pearl">Pindai / Cari Kartu Anggota</h2>

            <x-smart-scanner 
                name="member_return_code" 
                id="memberScanInput" 
                placeholder="Scan barcode / ketik NISN / NIP / Kode Anggota..." 
                label="Identitas Peminjam"
            />

            <p x-show="memberError" x-cloak class="mt-4 rounded-2xl bg-danger-soft/20 p-3 text-xs text-danger-red border border-danger-red/30" x-text="memberError"></p>
        </div>

        @if ($member)
            <div class="glass-panel rounded-4xl p-6">
                <div class="mb-5 flex items-center justify-between gap-4 border-b border-white/10 pb-4">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-azure-soft/15 text-xl font-bold text-azure-soft ring-2 ring-azure-soft/30">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-pearl text-base">{{ $member->name }}</p>
                            <p class="text-xs text-pearl/60">
                                {{ $member->member_code }} · {{ $member->type_label }}
                                @if ($member->department_class) · {{ $member->department_class }} @endif
                            </p>
                        </div>
                    </div>
                    <x-button href="{{ route('loans.return') }}" variant="secondary" class="text-xs">
                        🔄 Cari Anggota Lain
                    </x-button>
                </div>

                @if ($loans->isEmpty())
                    <div class="rounded-3xl border border-dashed border-white/10 p-10 text-center">
                        <p class="text-3xl">🎉</p>
                        <p class="mt-2 text-xs text-pearl/50">Tidak ada pinjaman aktif yang menunggak untuk anggota ini.</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('loans.return.store') }}" x-data="{ selected: [], fine: null }">
                        @csrf
                        <input type="hidden" name="member_id" value="{{ $member->id }}" />

                        <div class="space-y-6">
                            @foreach ($loans as $loan)
                                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 space-y-3">
                                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-white/5 pb-2">
                                        <div>
                                            <span class="font-mono text-sm font-bold text-azure-soft">{{ $loan->loan_code }}</span>
                                            <span class="ml-2 text-xs text-pearl/50">
                                                Jt Tempo: {{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}
                                            </span>
                                        </div>
                                        @if ($loan->is_late)
                                            <x-badge variant="red" dot>Terlambat {{ $loan->late_days }} Hari</x-badge>
                                        @else
                                            <x-badge variant="success" dot>Status Berjalan</x-badge>
                                        @endif
                                    </div>

                                    <div class="space-y-2">
                                        @foreach ($loan->items as $item)
                                            <div class="flex flex-col gap-3 rounded-2xl bg-white/5 p-4 sm:flex-row sm:items-center justify-between border border-white/5">
                                                <label class="flex flex-1 items-center gap-3 cursor-pointer">
                                                    <input
                                                        type="checkbox"
                                                        name="returned[]"
                                                        value="{{ $item->id }}"
                                                        @checked(!$item->bookItem || $item->status === 'dipinjam')
                                                        class="h-5 w-5 rounded border-white/20 bg-white/10 text-azure-soft focus:ring-azure-soft"
                                                    />
                                                    <div>
                                                        <span class="block text-sm font-semibold text-pearl">{{ $item->bookItem?->book?->title ?? '-' }}</span>
                                                        <span class="font-mono text-xs text-azure-soft">{{ $item->bookItem?->item_code }}</span>
                                                    </div>
                                                </label>

                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs text-pearl/50">Kondisi Akhir:</span>
                                                    <select
                                                        name="condition[{{ $item->id }}]"
                                                        class="input-debossed rounded-pill border-0 px-4 py-2 text-xs text-pearl"
                                                    >
                                                        <option value="baik">✅ Baik</option>
                                                        <option value="rusak">⚠️ Rusak</option>
                                                        <option value="hilang">❌ Hilang</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3">
                            <x-button href="{{ route('loans.return') }}" variant="secondary">Batal</x-button>
                            <x-button type="submit" variant="primary" class="px-8 font-bold">PROSES PENGEMBALIAN BUKU ➔</x-button>
                        </div>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <script>
        if (typeof window.registerReturnApp === 'undefined') {
            window.registerReturnApp = function() {
                if (typeof window.Alpine === 'undefined') return;

                window.Alpine.data('returnApp', () => ({
                    memberError: null,

                    init() {
                        window.addEventListener('barcode-scanned', (e) => {
                            this.scanMember(e.detail);
                        });
                    },

                    async scanMember(manualCode = null) {
                        const inputEl = document.getElementById('memberScanInput');
                        const code = manualCode || (inputEl ? inputEl.value.trim() : '');
                        if (!code) return;

                        this.memberError = null;

                        try {
                            const res = await fetch(`{{ route('loans.return.api.member') }}?code=${encodeURIComponent(code)}`);
                            const data = await res.json();

                            if (!data.found) {
                                this.memberError = data.message;
                                return;
                            }

                            window.location.href = data.redirect;
                        } catch (e) {
                            this.memberError = 'Gagal terhubung ke server.';
                        }
                    }
                }));
            };

            if (window.Alpine) {
                window.registerReturnApp();
            } else {
                document.addEventListener('alpine:init', window.registerReturnApp);
            }
        }
    </script>
</x-layouts.app>
<x-layouts.app>
    @section('page_title', 'Pengembalian Buku')

    <div
        x-data="returnApp()"
        x-init="init()"
        class="mx-auto max-w-4xl space-y-6"
    >
        <div class="glass-panel p-6">
            <div class="mb-4 flex items-center justify-between border-b-2 border-black pb-3">
                <h2 class="text-sm font-black uppercase tracking-wider text-black font-heading flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center border-2 border-black bg-brutal-yellow text-black text-xs font-mono font-black shadow-brutal-sm">📥</span>
                    Pindai Barcode Buku atau Kartu Anggota
                </h2>
                @if ($member)
                    <x-button href="{{ route('loans.return') }}" variant="secondary" class="text-xs shadow-brutal-sm">
                        🔄 Scan Buku / Anggota Lain
                    </x-button>
                @endif
            </div>

            <x-smart-scanner 
                name="member_return_code" 
                id="memberScanInput" 
                placeholder="Scan barcode buku / ketik kode eksemplar / NISN / NIP..." 
                label="Identitas / Barcode Buku yang Dikembalikan"
            />

            <p x-show="memberError" x-cloak class="mt-4 border-2 border-black bg-brutal-pink p-3 text-xs font-black uppercase text-white shadow-brutal-sm" x-text="memberError"></p>
        </div>

        @if ($member)
            <div class="glass-panel p-6">
                <div class="mb-5 flex items-center justify-between gap-4 border-b-2 border-black pb-4">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center border-2 border-black bg-brutal-yellow text-xl font-black font-mono text-black shadow-brutal-sm">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-black text-black text-base uppercase font-heading">{{ $member->name }}</p>
                            <p class="font-mono text-xs font-bold text-black/70">
                                {{ $member->member_code }} · {{ $member->type_label }}
                                @if ($member->department_class) · {{ $member->department_class }} @endif
                            </p>
                        </div>
                    </div>
                </div>

                @if ($loans->isEmpty())
                    <div class="border-2 border-dashed border-black bg-brutal-input p-10 text-center">
                        <p class="text-3xl">🎉</p>
                        <p class="mt-2 text-xs font-black uppercase text-black font-heading">Tidak Ada Pinjaman Aktif</p>
                        <p class="text-xs font-medium text-black/60 mt-1">Semua buku telah dikembalikan oleh anggota ini.</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('loans.return.store') }}" x-data="{ selected: [], fine: null }">
                        @csrf
                        <input type="hidden" name="member_id" value="{{ $member->id }}" />

                        <div class="space-y-6">
                            @foreach ($loans as $loan)
                                <div class="border-3 border-black bg-brutal-input p-5 shadow-brutal-sm space-y-3">
                                    <div class="flex flex-wrap items-center justify-between gap-2 border-b-2 border-black pb-2">
                                        <div>
                                            <span class="font-mono text-sm font-black text-black">{{ $loan->loan_code }}</span>
                                            <span class="ml-2 font-mono text-xs font-bold text-black/70">
                                                Jt Tempo: {{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}
                                            </span>
                                        </div>
                                        @if ($loan->is_late)
                                            <x-badge variant="red" dot>Terlambat {{ $loan->late_days }} Hari</x-badge>
                                        @else
                                            <x-badge variant="azure" dot>Status Berjalan</x-badge>
                                        @endif
                                    </div>

                                    <div class="space-y-2">
                                        @foreach ($loan->items as $item)
                                            <div class="flex flex-col gap-3 border-2 border-black bg-white p-4 sm:flex-row sm:items-center justify-between shadow-brutal-sm">
                                                <label class="flex flex-1 items-center gap-3 cursor-pointer">
                                                    <input
                                                        type="checkbox"
                                                        name="returned[]"
                                                        value="{{ $item->id }}"
                                                        @checked(!$item->bookItem || $item->status === 'dipinjam')
                                                        class="h-5 w-5 border-2 border-black text-black focus:ring-black accent-black cursor-pointer"
                                                    />
                                                    @if($item->bookItem?->book?->cover_url)
                                                        <img src="{{ $item->bookItem->book->cover_url }}" class="h-12 w-9 object-cover border-2 border-black shadow-brutal-sm" />
                                                    @else
                                                        <span class="flex h-12 w-9 items-center justify-center border-2 border-black bg-brutal-input text-base">📖</span>
                                                    @endif
                                                    <div>
                                                        <span class="block text-sm font-black uppercase text-black font-heading">{{ $item->bookItem?->book?->title ?? '-' }}</span>
                                                        <span class="font-mono text-xs font-bold text-black/70">{{ $item->bookItem?->item_code }}</span>
                                                    </div>
                                                </label>

                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-black uppercase text-black">Kondisi Akhir:</span>
                                                    <select
                                                        name="condition[{{ $item->id }}]"
                                                        class="input-debossed border-2 border-black bg-white px-3 py-2 text-xs font-bold text-black cursor-pointer"
                                                    >
                                                        <option value="baik" @selected($item->condition === 'baik')>Baik</option>
                                                        <option value="rusak" @selected($item->condition === 'rusak')>Rusak</option>
                                                        <option value="hilang" @selected($item->condition === 'hilang')>Hilang</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Panel Pembayaran Denda (Jika Ada) --}}
                        @if ($totalFine > 0)
                            <div class="mt-6 border-3 border-black bg-brutal-pink/20 p-5 shadow-brutal">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-black uppercase text-black">Kalkulasi Denda Keterlambatan</p>
                                        <p class="font-mono text-2xl font-black text-black">Rp {{ number_format($totalFine, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <label class="btn-brutal inline-flex items-center gap-2 border-2 border-black bg-white px-3 py-2 text-xs font-black uppercase tracking-wider cursor-pointer shadow-brutal-sm">
                                            <input type="radio" name="fine_action" value="pay" checked class="accent-black" />
                                            <span>Bayar Lunas</span>
                                        </label>
                                        <label class="btn-brutal inline-flex items-center gap-2 border-2 border-black bg-white px-3 py-2 text-xs font-black uppercase tracking-wider cursor-pointer shadow-brutal-sm">
                                            <input type="radio" name="fine_action" value="waive" class="accent-black" />
                                            <span>Bebaskan (Dispensasi)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mt-6 flex justify-end gap-3 pt-3">
                            <x-button href="{{ route('loans.return') }}" variant="secondary" class="shadow-brutal">Batal</x-button>
                            <x-button type="submit" variant="primary" class="shadow-brutal px-8 py-3 text-xs font-black">
                                PROSES PENGEMBALIAN BUKU ➔
                            </x-button>
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
                        const inputEl = document.getElementById('memberScanInput');
                        if (inputEl) inputEl.focus();

                        const handleScan = (e) => {
                            const val = e.detail?.code || e.detail?.barcode || e.detail;
                            if (val) this.search(val);
                        };

                        window.addEventListener('scan-memberScanInput', handleScan);
                        window.addEventListener('barcode-scanned', handleScan);
                    },

                    search(query) {
                        if (!query) return;
                        this.memberError = null;

                        const url = '{{ route("loans.return.api.member") }}?query=' + encodeURIComponent(query);
                        fetch(url)
                            .then(res => res.json())
                            .then(data => {
                                if (data.found && data.member) {
                                    window.location.href = '{{ route("loans.return") }}?member_id=' + data.member.id;
                                } else {
                                    this.memberError = data.message || 'Data anggota atau buku aktif tidak ditemukan.';
                                }
                            })
                            .catch(() => {
                                this.memberError = 'Gagal memproses pemindaian. Coba lagi.';
                            });
                    }
                }));
            };

            window.registerReturnApp();
        }

        document.addEventListener('page:loaded', () => {
            if (typeof window.registerReturnApp === 'function') {
                window.registerReturnApp();
            }
        });
    </script>
</x-layouts.app>
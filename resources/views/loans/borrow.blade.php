<x-layouts.app>
    @section('page_title', 'Peminjaman Buku (POS)')

    <div
        x-data="borrowApp()"
        x-init="init()"
        class="mx-auto max-w-5xl space-y-6"
    >
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Panel Kiri: Peminjam (Alur 2-Step) --}}
            <div class="glass-panel p-6">
                <div class="mb-4 flex items-center justify-between border-b-2 border-black pb-3">
                    <h2 class="text-sm font-black uppercase tracking-wider text-black font-heading flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center border-2 border-black bg-brutal-yellow text-black text-xs font-mono font-black shadow-brutal-sm">1</span>
                        Identifikasi Anggota
                    </h2>
                    <template x-if="member">
                        <button type="button" @click="resetMember()" class="border-2 border-black bg-white px-2.5 py-1 text-xs font-black uppercase text-black hover:bg-brutal-input shadow-brutal-sm cursor-pointer">
                            🔄 Ganti Anggota
                        </button>
                    </template>
                </div>

                {{-- STEP 1: Pilih Kategori Anggota --}}
                <div class="mb-5 space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-black">
                        Kategori Peminjam <span class="text-brutal-pink">*</span>
                    </label>
                    <div class="grid grid-cols-4 gap-2">
                        <template x-for="cat in categories" :key="cat.id">
                            <button
                                type="button"
                                @click="selectCategory(cat.id)"
                                :class="selectedCategory === cat.id 
                                    ? 'bg-brutal-yellow text-black border-3 border-black font-black shadow-brutal-sm scale-[0.98]' 
                                    : 'bg-white text-black border-2 border-black hover:bg-brutal-input shadow-brutal-sm font-bold'"
                                class="flex flex-col items-center justify-center py-3 px-2 text-center transition-all duration-75 active:scale-95 cursor-pointer"
                            >
                                <span class="text-xl" x-text="cat.icon"></span>
                                <span class="mt-1 text-xs uppercase font-heading" x-text="cat.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- STEP 2: Input / Scan Identitas --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-black uppercase tracking-wider text-black">
                            Identitas (<span x-text="categoryLabel" class="text-black bg-brutal-neon px-1 border border-black font-mono"></span>)
                        </label>
                    </div>

                    <x-smart-scanner 
                        name="member_code_input" 
                        id="memberScanInput" 
                        placeholder="Scan barcode / ketik NISN / NIP / NIK..." 
                        label="Nomor Induk / Kartu Anggota"
                    />
                </div>

                {{-- Data Detail Anggota Terpilih --}}
                <div x-show="member" x-cloak class="mt-5 border-3 border-black bg-brutal-neon/15 p-5 shadow-brutal">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center border-2 border-black bg-brutal-yellow text-xl font-black font-mono text-black shadow-brutal-sm">
                            <span x-text="initials(member?.name)"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-black text-black text-base uppercase font-heading" x-text="member?.name"></p>
                            <p class="font-mono text-xs font-bold text-black/70" x-text="(member?.member_code || '') + ' · ' + (member?.type || '') + (member?.department_class ? ' (' + member.department_class + ')' : '')"></p>
                        </div>
                        <span class="shrink-0 border-2 border-black px-3 py-1 text-xs font-black uppercase tracking-wider shadow-brutal-sm"
                            :class="eligibility.ok ? 'bg-brutal-neon text-black' : 'bg-brutal-pink text-white'"
                            x-text="eligibility.ok ? 'Memenuhi Syarat' : 'Dibatasi'"></span>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-3 text-center">
                        <div class="border-2 border-black bg-white p-3 shadow-brutal-sm">
                            <p class="font-mono text-base font-black text-black" x-text="(member?.active_loans || 0) + '/' + (member?.quota || 0)"></p>
                            <p class="text-[10px] font-black uppercase tracking-wider text-black/60">Buku Dipinjam</p>
                        </div>
                        <div class="border-2 border-black bg-white p-3 shadow-brutal-sm">
                            <p class="font-mono text-base font-black text-black" x-text="member?.remaining_quota || 0"></p>
                            <p class="text-[10px] font-black uppercase tracking-wider text-black/60">Sisa Kuota</p>
                        </div>
                        <div class="border-2 border-black bg-white p-3 shadow-brutal-sm">
                            <p class="font-mono text-xs font-black text-black mt-1" x-text="dueDate"></p>
                            <p class="text-[10px] font-black uppercase tracking-wider text-black/60">Jatuh Tempo</p>
                        </div>
                    </div>

                    <template x-if="!eligibility.ok && member">
                        <p class="mt-4 border-2 border-black bg-brutal-pink p-3 text-xs font-black uppercase text-white shadow-brutal-sm" x-text="eligibility.message"></p>
                    </template>
                </div>

                <p x-show="memberError" x-cloak class="mt-4 border-2 border-black bg-brutal-pink p-3 text-xs font-black uppercase text-white shadow-brutal-sm" x-text="memberError"></p>
            </div>

            {{-- Panel Kanan: Keranjang Peminjaman --}}
            <div class="glass-panel p-6">
                <div class="mb-4 flex items-center justify-between border-b-2 border-black pb-3">
                    <h2 class="text-sm font-black uppercase tracking-wider text-black font-heading flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center border-2 border-black bg-brutal-yellow text-black text-xs font-mono font-black shadow-brutal-sm">2</span>
                        Scan / Input Kode Buku
                    </h2>
                    <span class="font-mono text-xs font-bold text-black/50" x-text="cart.length + ' ITEM'"></span>
                </div>

                <div :class="{ 'opacity-50 pointer-events-none': !member || !eligibility.ok }">
                    <x-smart-scanner 
                        name="book_code_input" 
                        id="scanBookInput" 
                        placeholder="Scan barcode / ketik kode buku (mis. BK-0001)..." 
                        label="Kode Buku / Barcode Eksemplar"
                    />
                </div>

                <p x-show="bookError" x-cloak class="mt-3 border-2 border-black bg-brutal-pink p-3 text-xs font-black uppercase text-white shadow-brutal-sm" x-text="bookError"></p>

                <div class="mt-6 space-y-2 max-h-[280px] overflow-y-auto pr-1">
                    <template x-for="(item, idx) in cart" :key="item.item_code">
                        <div class="flex items-center gap-3 border-2 border-black bg-white p-3 shadow-brutal-sm">
                            <span class="w-5 font-mono text-xs font-black text-black" x-text="idx + 1"></span>
                            <div class="flex h-12 w-9 shrink-0 items-center justify-center border-2 border-black bg-brutal-input text-xs overflow-hidden">
                                <template x-if="item.cover_url">
                                    <img :src="item.cover_url" class="h-full w-full object-cover" />
                                </template>
                                <template x-if="!item.cover_url">
                                    <span>📖</span>
                                </template>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-black text-black uppercase font-heading" x-text="item.title"></p>
                                <p class="font-mono text-xs font-bold text-black/70" x-text="item.item_code + ' · ' + (item.author || '-')"></p>
                            </div>
                            <button type="button" @click="cart.splice(idx, 1)" class="border-2 border-black bg-brutal-pink p-2 text-white hover:bg-red-700 shadow-brutal-sm cursor-pointer" title="Hapus dari keranjang">🗑</button>
                        </div>
                    </template>

                    <div x-show="cart.length === 0" x-cloak class="border-2 border-dashed border-black bg-brutal-input p-8 text-center">
                        <p class="text-3xl">📚</p>
                        <p class="mt-2 text-xs font-black uppercase text-black font-heading">Keranjang Pinjam Kosong</p>
                        <p class="text-xs font-medium text-black/60 mt-1">Ketik kode buku atau scan barcode untuk menambahkan.</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between gap-4 border-3 border-black bg-brutal-yellow p-4 shadow-brutal">
                    <div>
                        <p class="text-[10px] font-black uppercase text-black">Total Item Buku</p>
                        <p class="font-mono text-3xl font-black text-black" x-text="cart.length"></p>
                    </div>
                    <x-button
                        type="button"
                        variant="primary"
                        class="px-8 py-3 text-xs font-black shadow-brutal"
                        @click="submit()"
                        x-bind:disabled="!canSubmit()"
                    >
                        PROSES TRANSAKSI PINJAM ➔
                    </x-button>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('loans.store') }}" id="loanForm" class="hidden">
            @csrf
            <input type="hidden" name="member_id" :value="member?.id" />
            <template x-for="item in cart" :key="item.item_code">
                <input type="hidden" name="items[]" :value="item.id" />
            </template>
        </form>
    </div>

    <script>
        if (typeof window.registerBorrowApp === 'undefined') {
            window.registerBorrowApp = function() {
                if (typeof window.Alpine === 'undefined') return;

                window.Alpine.data('borrowApp', () => ({
                    categories: [
                        { id: 'siswa', label: 'Siswa', icon: '🎓' },
                        { id: 'guru', label: 'Guru', icon: '👨‍🏫' },
                        { id: 'staf', label: 'Staf', icon: '💼' },
                        { id: 'tamu', label: 'Tamu', icon: '👤' },
                    ],
                    selectedCategory: 'siswa',
                    categoryLabel: 'Siswa',
                    member: null,
                    eligibility: { ok: false },
                    memberError: null,
                    cart: [],
                    bookError: null,
                    dueDate: '—',

                    init() {
                        // Handle member scanner event
                        window.addEventListener('scan-memberScanInput', (e) => {
                            this.scanMember(e.detail);
                        });

                        // Handle book scanner event
                        window.addEventListener('scan-scanBookInput', (e) => {
                            this.scanBook(e.detail);
                        });

                        // General fallback
                        window.addEventListener('barcode-scanned', (e) => {
                            const code = typeof e.detail === 'string' ? e.detail : (e.detail?.code || '');
                            if (!code) return;
                            if (!this.member) {
                                this.scanMember(code);
                            } else {
                                this.scanBook(code);
                            }
                        });
                    },

                    selectCategory(catId) {
                        this.selectedCategory = catId;
                        const catObj = this.categories.find(c => c.id === catId);
                        this.categoryLabel = catObj ? catObj.label : catId;
                        this.member = null;
                        this.eligibility = { ok: false };
                        this.memberError = null;

                        this.$nextTick(() => {
                            const input = document.getElementById('memberScanInput');
                            if (input) input.focus();
                        });
                    },

                    resetMember() {
                        this.member = null;
                        this.eligibility = { ok: false };
                        this.cart = [];
                        this.memberError = null;
                        this.bookError = null;
                        this.$nextTick(() => {
                            const input = document.getElementById('memberScanInput');
                            if (input) {
                                input.value = '';
                                input.focus();
                            }
                        });
                    },

                    initials(name) {
                        if (!name) return '?';
                        return name.trim().split(/\s+/).slice(0, 2).map(w => w[0].toUpperCase()).join('');
                    },

                    async scanMember(manualCode = null) {
                        const inputEl = document.getElementById('memberScanInput');
                        const code = manualCode || (inputEl ? inputEl.value.trim() : '');
                        if (!code) return;

                        this.memberError = null;

                        try {
                            const url = `{{ route('loans.api.member') }}?code=${encodeURIComponent(code)}&type=${encodeURIComponent(this.selectedCategory)}`;
                            const res = await fetch(url);
                            const data = await res.json();

                            if (!data.found) {
                                this.member = null;
                                this.eligibility = { ok: false };
                                this.memberError = data.message || 'Anggota tidak ditemukan.';
                                return;
                            }

                            this.member = data.member;
                            this.eligibility = data.eligibility;
                            this.cart = [];
                            this.bookError = null;

                            if (data.member.raw_type) {
                                this.selectedCategory = data.member.raw_type;
                                const catObj = this.categories.find(c => c.id === data.member.raw_type);
                                if (catObj) this.categoryLabel = catObj.label;
                            }

                            if (data.eligibility.ok) {
                                this.dueDate = this.computeDueDate();
                                this.$nextTick(() => {
                                    const bookInput = document.getElementById('scanBookInput');
                                    if (bookInput) bookInput.focus();
                                });
                            }
                        } catch (e) {
                            this.memberError = 'Gagal terhubung ke server.';
                        }
                    },

                    async scanBook(manualCode = null) {
                        if (!this.member) {
                            this.bookError = 'Silakan pilih/scan identitas peminjam terlebih dahulu.';
                            return;
                        }

                        if (!this.eligibility.ok) {
                            this.bookError = this.eligibility.message || 'Peminjam tidak memenuhi syarat peminjaman.';
                            return;
                        }

                        const inputEl = document.getElementById('scanBookInput');
                        const code = manualCode || (inputEl ? inputEl.value.trim() : '');
                        if (!code) return;

                        this.bookError = null;

                        try {
                            const res = await fetch(`{{ route('loans.api.book') }}?code=${encodeURIComponent(code)}`);
                            const data = await res.json();

                            if (!data.found) {
                                this.bookError = data.message;
                                return;
                            }

                            if (this.cart.some(i => i.item_code === data.item.item_code)) {
                                this.bookError = `Eksemplar '${data.item.item_code}' sudah ada di keranjang.`;
                                return;
                            }

                            if (this.cart.length >= this.member.remaining_quota) {
                                this.bookError = `Jumlah buku melebihi sisa kuota (${this.member.remaining_quota} buku).`;
                                return;
                            }

                            this.cart.push(data.item);

                            // Clear input for next entry
                            if (inputEl) {
                                inputEl.value = '';
                                this.$nextTick(() => inputEl.focus());
                            }
                        } catch (e) {
                            this.bookError = 'Gagal terhubung ke server.';
                        }
                    },

                    computeDueDate() {
                        const days = Number(this.member?.days_per_loan) || 7;
                        const date = new Date();
                        date.setDate(date.getDate() + days);
                        return String(date.getDate()).padStart(2, '0') + '-' +
                            String(date.getMonth() + 1).padStart(2, '0') + '-' + date.getFullYear();
                    },

                    canSubmit() {
                        return this.member && this.eligibility.ok && this.cart.length > 0;
                    },

                    submit() {
                        if (!this.canSubmit()) return;
                        document.getElementById('loanForm').submit();
                    }
                }));
            };

            if (window.Alpine) {
                window.registerBorrowApp();
            } else {
                document.addEventListener('alpine:init', window.registerBorrowApp);
            }
        }
    </script>
</x-layouts.app>
<x-layouts.app>
    @section('page_title', 'Peminjaman Buku (POS)')

    <div
        x-data="borrowApp()"
        x-init="init()"
        class="mx-auto max-w-5xl space-y-6"
    >
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Panel Kiri: Peminjam (Alur 2-Step) --}}
            <div class="glass-panel rounded-4xl p-6">
                <h2 class="mb-4 text-lg font-bold text-pearl flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-azure-soft/20 text-azure-soft text-xs font-bold">1</span>
                    Identifikasi Anggota
                </h2>

                {{-- STEP 1: Pilih Kategori Anggota (Tactile Skeuomorphic Buttons) --}}
                <div class="mb-5 space-y-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-pearl/80">
                        Step 1: Pilih Kategori Peminjam <span class="text-amber-warm">*</span>
                    </label>
                    <div class="grid grid-cols-4 gap-2">
                        <template x-for="cat in categories" :key="cat.id">
                            <button
                                type="button"
                                @click="selectCategory(cat.id)"
                                :class="selectedCategory === cat.id 
                                    ? 'bg-azure-soft text-navy-dark border-azure-soft font-extrabold shadow-[inset_0_2px_4px_rgba(0,0,0,0.4)] scale-[0.98]' 
                                    : 'bg-navy-dark/80 text-pearl border-white/10 hover:bg-white/10 shadow-[0_4px_6px_rgba(0,0,0,0.3)] font-medium'"
                                class="flex flex-col items-center justify-center rounded-2xl border py-3 px-2 text-center transition-all duration-150 active:scale-95 cursor-pointer"
                            >
                                <span class="text-xl" x-text="cat.icon"></span>
                                <span class="mt-1 text-xs" x-text="cat.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- STEP 2: Input / Scan Identitas --}}
                <div class="space-y-3" :class="{ 'opacity-40 pointer-events-none': !selectedCategory }">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-pearl/80">
                            Step 2: Identitas (<span x-text="categoryLabel" class="text-azure-soft font-bold"></span>)
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
                <div x-show="member" x-cloak class="mt-5 rounded-3xl border border-white/10 bg-white/5 p-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-azure-soft/15 text-xl font-bold text-azure-soft ring-2 ring-azure-soft/30">
                            <span x-text="initials(member.name)"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-pearl text-base" x-text="member.name"></p>
                            <p class="text-xs text-pearl/60" x-text="member.member_code + ' · ' + member.type + (member.department_class ? ' (' + member.department_class + ')' : '')"></p>
                        </div>
                        <span class="shrink-0 rounded-pill px-3 py-1 text-xs font-semibold"
                            :class="eligibility.ok ? 'bg-success-soft text-success-green' : 'bg-danger-soft text-danger-red'"
                            x-text="eligibility.ok ? 'Memenuhi Syarat' : 'Dibatasi'"></span>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-2xl bg-white/5 p-3">
                            <p class="text-base font-bold text-pearl" x-text="member.active_loans + '/' + member.quota"></p>
                            <p class="text-[10px] uppercase tracking-wider text-pearl/50">Buku Dipinjam</p>
                        </div>
                        <div class="rounded-2xl bg-white/5 p-3">
                            <p class="text-base font-bold text-success-green" x-text="member.remaining_quota"></p>
                            <p class="text-[10px] uppercase tracking-wider text-pearl/50">Sisa Kuota</p>
                        </div>
                        <div class="rounded-2xl bg-white/5 p-3">
                            <p class="text-base font-bold text-amber-warm" x-text="dueDate"></p>
                            <p class="text-[10px] uppercase tracking-wider text-pearl/50">Jatuh Tempo</p>
                        </div>
                    </div>

                    <template x-if="!eligibility.ok">
                        <p class="mt-4 rounded-2xl bg-danger-soft/20 p-3 text-xs text-danger-red border border-danger-red/30" x-text="eligibility.message"></p>
                    </template>
                </div>

                <p x-show="memberError" x-cloak class="mt-4 rounded-2xl bg-danger-soft/20 p-3 text-xs text-danger-red border border-danger-red/30" x-text="memberError"></p>
            </div>

            {{-- Panel Kanan: Keranjang Peminjaman --}}
            <div class="glass-panel rounded-4xl p-6">
                <h2 class="mb-4 text-lg font-bold text-pearl flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-azure-soft/20 text-azure-soft text-xs font-bold">2</span>
                    Scan Koleksi Buku
                </h2>

                <div :class="{ 'opacity-40 pointer-events-none': !eligibility.ok || !member }">
                    <x-smart-scanner 
                        name="book_code_input" 
                        id="scanBookInput" 
                        placeholder="Scan barcode eksemplar buku..." 
                        label="Kode Barcode Eksemplar Buku"
                    />
                </div>

                <p x-show="bookError" x-cloak class="mt-3 rounded-2xl bg-danger-soft/20 p-3 text-xs text-danger-red border border-danger-red/30" x-text="bookError"></p>

                <div class="mt-6 space-y-2">
                    <template x-for="(item, idx) in cart" :key="item.item_code">
                        <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                            <span class="w-5 text-xs font-bold text-pearl/40" x-text="idx + 1"></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-pearl" x-text="item.title"></p>
                                <p class="font-mono text-xs text-azure-soft" x-text="item.item_code + ' · ' + (item.author || '-')"></p>
                            </div>
                            <button type="button" @click="cart.splice(idx, 1)" class="shrink-0 rounded-xl p-2 text-pearl/40 transition hover:bg-danger-soft hover:text-danger-red">🗑</button>
                        </div>
                    </template>

                    <div x-show="cart.length === 0" x-cloak class="rounded-3xl border border-dashed border-white/10 p-8 text-center">
                        <p class="text-3xl">📚</p>
                        <p class="mt-2 text-xs text-pearl/40">Keranjang pinjam kosong. Pindai barcode eksemplar untuk menambahkan.</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between gap-4 rounded-3xl border border-azure-glow/20 bg-azure-glow/10 p-4">
                    <div>
                        <p class="text-xs text-pearl/50">Total Item Buku</p>
                        <p class="text-2xl font-bold text-pearl" x-text="cart.length"></p>
                    </div>
                    <x-button
                        type="button"
                        variant="primary"
                        class="px-8 py-3 text-sm font-bold"
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
                        window.addEventListener('barcode-scanned', (e) => {
                            const code = e.detail;
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
                                this.memberError = data.message || 'Anggota tidak ditemukan di kategori ini.';
                                return;
                            }

                            this.member = data.member;
                            this.eligibility = data.eligibility;
                            this.cart = [];
                            this.bookError = null;

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
                        const inputEl = document.getElementById('scanBookInput');
                        const code = manualCode || (inputEl ? inputEl.value.trim() : '');
                        if (!code) return;

                        this.bookError = null;
                        if (inputEl) inputEl.value = '';

                        try {
                            const res = await fetch(`{{ route('loans.api.book') }}?code=${encodeURIComponent(code)}`);
                            const data = await res.json();

                            if (!data.found) {
                                this.bookError = data.message;
                                return;
                            }

                            if (this.cart.some(i => i.item_code === data.item.item_code)) {
                                this.bookError = 'Eksemplar buku ini sudah ada di keranjang.';
                                return;
                            }

                            if (this.cart.length >= this.member.remaining_quota) {
                                this.bookError = 'Jumlah buku melebihi batas kuota peminjaman anggota.';
                                return;
                            }

                            this.cart.push(data.item);
                            this.$nextTick(() => {
                                if (inputEl) inputEl.focus();
                            });
                        } catch (e) {
                            this.bookError = 'Gagal terhubung ke server.';
                        }
                    },

                    computeDueDate() {
                        const days = Number(this.member.days_per_loan) || 7;
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
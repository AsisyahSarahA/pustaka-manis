<x-layouts.kiosk>
    {{-- Arcade Cabinet Home View --}}
    <div class="w-full max-w-4xl px-4 text-center" x-show="step === 'home'">
        {{-- Header Marquee Block --}}
        <div class="mb-10 border-4 border-black bg-white p-6 shadow-brutal-xl">
            @if ($logo = setting('app_logo'))
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center border-3 border-black bg-brutal-yellow p-2 shadow-brutal">
                    <img src="{{ asset($logo) }}" alt="Logo" class="h-16 w-16 object-contain">
                </div>
            @else
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center border-3 border-black bg-brutal-yellow text-black font-black shadow-brutal">
                    <x-icon name="landmark" class="h-10 w-10" />
                </div>
            @endif
            <div class="inline-block border-2 border-black bg-black px-4 py-1 font-mono text-xs font-black text-brutal-yellow uppercase tracking-widest mb-3">
                [ SYSTEM KIOSK TERMINAL v2.0 ]
            </div>
            <h1 class="font-heading text-3xl font-black tracking-tight text-black sm:text-5xl uppercase">SELAMAT DATANG DI</h1>
            <p class="mt-2 font-heading text-xl font-black uppercase text-brutal-blue tracking-wide">PERPUSTAKAAN {{ setting('school_name', 'SMP') }}</p>
            <p class="mt-2 font-mono text-xs font-extrabold uppercase tracking-widest text-black/70">► TEKAN TOMBOL DI BAWAH UNTUK CHECK-IN KUNJUNGAN ◄</p>
        </div>

        {{-- Arcade Touch Selection Buttons --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            {{-- SISWA --}}
            <button
                type="button"
                @click="step = 'form'; visitorType = 'siswa'; focusInput();"
                class="btn-brutal border-4 border-black bg-brutal-neon p-8 shadow-brutal-lg text-black transition-all duration-100 ease-linear hover:-translate-y-1 hover:shadow-brutal-xl active:translate-y-2 active:shadow-none cursor-pointer"
            >
                <div class="mx-auto flex h-16 w-16 items-center justify-center border-3 border-black bg-white text-black shadow-brutal-sm">
                    <x-icon name="graduation-cap" class="h-9 w-9" />
                </div>
                <p class="mt-4 font-heading text-2xl font-black uppercase tracking-wider text-black">[ SISWA ]</p>
                <p class="mt-1 font-mono text-xs font-bold uppercase tracking-wide text-black/80">Scan Kartu / NISN</p>
            </button>

            {{-- GURU --}}
            <button
                type="button"
                @click="step = 'form'; visitorType = 'guru'; focusInput();"
                class="btn-brutal border-4 border-black bg-brutal-yellow p-8 shadow-brutal-lg text-black transition-all duration-100 ease-linear hover:-translate-y-1 hover:shadow-brutal-xl active:translate-y-2 active:shadow-none cursor-pointer"
            >
                <div class="mx-auto flex h-16 w-16 items-center justify-center border-3 border-black bg-white text-black shadow-brutal-sm">
                    <x-icon name="presentation" class="h-9 w-9" />
                </div>
                <p class="mt-4 font-heading text-2xl font-black uppercase tracking-wider text-black">[ GURU / STAF ]</p>
                <p class="mt-1 font-mono text-xs font-bold uppercase tracking-wide text-black/80">Scan Kartu / NIP</p>
            </button>

            {{-- TAMU --}}
            <button
                type="button"
                @click="step = 'form'; visitorType = 'tamu'"
                class="btn-brutal border-4 border-black bg-brutal-blue p-8 shadow-brutal-lg text-white transition-all duration-100 ease-linear hover:-translate-y-1 hover:shadow-brutal-xl active:translate-y-2 active:shadow-none cursor-pointer"
            >
                <div class="mx-auto flex h-16 w-16 items-center justify-center border-3 border-black bg-black text-brutal-yellow shadow-brutal-sm">
                    <x-icon name="user-round" class="h-9 w-9" />
                </div>
                <p class="mt-4 font-heading text-2xl font-black uppercase tracking-wider text-white">[ TAMU ]</p>
                <p class="mt-1 font-mono text-xs font-bold uppercase tracking-wide text-white/90">Kunjungan Luar</p>
            </button>
        </div>

        {{-- Arcade Footer Stats --}}
        <div class="mt-10 border-3 border-black bg-white p-4 font-mono text-xs font-black uppercase tracking-widest text-black shadow-brutal flex flex-wrap items-center justify-center gap-6">
            <span class="inline-flex items-center gap-2 border-r-2 border-black pr-6"><x-icon name="calendar" class="h-4 w-4 text-brutal-blue" /> {{ now()->translatedFormat('l, d F Y') }}</span>
            <span class="inline-flex items-center gap-2 border-r-2 border-black pr-6"><x-icon name="clock" class="h-4 w-4 text-brutal-pink" /> <span x-text="clock"></span></span>
            <span class="inline-flex items-center gap-2"><x-icon name="users" class="h-4 w-4 text-brutal-neon" /> PENGUNJUNG HARI INI: <span class="bg-black px-2 py-0.5 text-brutal-yellow font-black">{{ $todayVisitors }}</span></span>
        </div>
    </div>

    {{-- Form Siswa/Guru (Integrated Smart Scanner) --}}
    <div class="w-full max-w-lg px-4" x-show="step === 'form' && visitorType !== 'tamu'" x-cloak>
        <div class="border-4 border-black bg-white p-8 shadow-brutal-xl space-y-5">
            <div class="flex items-center justify-between border-b-4 border-black pb-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center border-2 border-black bg-brutal-neon font-black text-black">
                        <template x-if="visitorType === 'siswa'"><x-icon name="graduation-cap" class="h-7 w-7" /></template>
                        <template x-if="visitorType !== 'siswa'"><x-icon name="presentation" class="h-7 w-7" /></template>
                    </span>
                    <h2 class="font-heading text-xl font-black uppercase text-black" x-text="visitorType === 'siswa' ? 'CHECK-IN SISWA' : 'CHECK-IN GURU / STAF'"></h2>
                </div>
                <span class="font-mono text-xs font-black bg-black text-white px-2 py-1">[READY]</span>
            </div>

            <x-smart-scanner 
                name="kiosk_identity_input" 
                id="identityInput" 
                placeholder="Scan barcode / ketik NISN / NIP..." 
                label="SCAN ATAU KETIK NOMOR IDENTITAS"
            />

            <div class="mt-6 flex gap-3">
                <button type="button" @click="reset()" class="btn-brutal w-1/3 border-3 border-black bg-white px-4 py-3 text-xs font-extrabold uppercase text-black shadow-brutal hover:bg-gray-100 active:translate-y-1 active:shadow-none cursor-pointer">
                    ◄ BATAL
                </button>
                <button type="button" @click="submitMember()" :disabled="loading" class="btn-brutal w-2/3 border-3 border-black bg-brutal-neon px-6 py-3 text-xs font-black uppercase text-black shadow-brutal hover:bg-emerald-300 active:translate-y-1 active:shadow-none cursor-pointer">
                    <span x-show="!loading">MASUK KUNJUNGAN ➔</span>
                    <span x-show="loading">MEMPROSES...</span>
                </button>
            </div>

            <p x-show="error" x-cloak class="mt-4 border-3 border-black bg-brutal-pink p-3 font-mono text-xs font-bold text-white shadow-brutal" x-text="'🚨 ERROR: ' + error"></p>
        </div>
    </div>

    {{-- Form Tamu --}}
    <div class="w-full max-w-lg px-4" x-show="step === 'form' && visitorType === 'tamu'" x-cloak>
        <div class="border-4 border-black bg-white p-8 shadow-brutal-xl space-y-4">
            <div class="flex items-center gap-3 border-b-4 border-black pb-3">
                <span class="flex h-12 w-12 items-center justify-center border-2 border-black bg-brutal-blue text-white">
                    <x-icon name="user-round" class="h-7 w-7" />
                </span>
                <div>
                    <h2 class="font-heading text-xl font-black uppercase text-black">CHECK-IN TAMU EKSTERNAL</h2>
                    <p class="font-mono text-xs font-bold text-black/60 uppercase">ISI DATA DIRI UNTUK KUNJUNGAN</p>
                </div>
            </div>

            <div class="space-y-4 pt-2">
                <x-input
                    type="text"
                    x-model="guest.name"
                    placeholder="Contoh: Budi Santoso"
                    label="NAMA LENGKAP *"
                    autocomplete="off"
                />
                <x-input
                    type="text"
                    x-model="guest.origin"
                    placeholder="Contoh: Dinas Pendidikan / Univ XYZ"
                    label="INSTANSI ASAL *"
                    autocomplete="off"
                />
                <div>
                    <label class="mb-1.5 block font-mono text-xs font-black uppercase tracking-widest text-black">
                        ❯ TUJUAN KUNJUNGAN *
                    </label>
                    <select x-model="guest.purpose" class="input-debossed w-full border-3 border-black px-4 py-2.5 text-sm font-semibold text-black bg-white">
                        <option value="">-- PILIH TUJUAN KUNJUNGAN --</option>
                        <option value="penelitian">PENELITIAN</option>
                        <option value="studi banding">STUDI BANDING</option>
                        <option value="kunjungan resmi">KUNJUNGAN RESMI</option>
                        <option value="lainnya">LAINNYA</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex gap-3 pt-2">
                <button type="button" @click="reset()" class="btn-brutal w-1/3 border-3 border-black bg-white px-4 py-3 text-xs font-extrabold uppercase text-black shadow-brutal hover:bg-gray-100 active:translate-y-1 active:shadow-none cursor-pointer">
                    ◄ BATAL
                </button>
                <button type="button" @click="submitGuest()" :disabled="loading" class="btn-brutal w-2/3 border-3 border-black bg-brutal-yellow px-6 py-3 text-xs font-black uppercase text-black shadow-brutal hover:bg-yellow-300 active:translate-y-1 active:shadow-none cursor-pointer">
                    <span x-show="!loading">SUBMIT KUNJUNGAN ➔</span>
                    <span x-show="loading">MEMPROSES...</span>
                </button>
            </div>

            <p x-show="error" x-cloak class="mt-4 border-3 border-black bg-brutal-pink p-3 font-mono text-xs font-bold text-white shadow-brutal" x-text="'🚨 ERROR: ' + error"></p>
        </div>
    </div>

    {{-- Sukses Arcade Stamp Banner --}}
    <div class="w-full max-w-lg px-4" x-show="step === 'success'" x-cloak>
        <div class="border-4 border-black bg-brutal-neon p-10 text-center shadow-brutal-xl text-black">
            <div class="mx-auto flex h-24 w-24 items-center justify-center border-4 border-black bg-white text-black shadow-brutal">
                <x-icon name="check" class="h-14 w-14" />
            </div>
            <h2 class="mt-6 font-heading text-3xl font-black uppercase text-black tracking-tight" x-text="successMessage"></h2>
            <p class="mt-2 font-mono text-sm font-black uppercase text-black/80" x-text="successTime"></p>
            <div class="mt-6 inline-block border-3 border-black bg-brutal-yellow px-6 py-2 font-mono text-xs font-black text-black uppercase tracking-widest shadow-brutal">
                ⚡ TERIMA KASIH TELAH BERKUNJUNG! ⚡
            </div>
        </div>
    </div>

    <script>
        function kioskApp() {
            return {
                step: 'home',
                visitorType: 'siswa',
                guest: { name: '', origin: '', purpose: '' },
                error: null,
                loading: false,
                successMessage: '',
                successTime: '',
                clock: '',
                timer: null,
                clockTimer: null,

                init() {
                    this.updateClock();
                    this.clockTimer = setInterval(() => this.updateClock(), 1000);
                    document.addEventListener('keydown', this.handleKioskKey.bind(this));

                    window.addEventListener('scan-identityInput', (e) => {
                        if (this.step === 'form' && this.visitorType !== 'tamu') {
                            this.submitMember(e.detail);
                        }
                    });

                    window.addEventListener('barcode-scanned', (e) => {
                        if (this.step === 'form' && this.visitorType !== 'tamu') {
                            const code = typeof e.detail === 'string' ? e.detail : (e.detail?.code || '');
                            if (code) this.submitMember(code);
                        }
                    });
                },

                handleKioskKey(e) {
                    if (e.key === 'Escape') this.reset();
                },

                focusInput() {
                    this.$nextTick(() => {
                        const el = document.getElementById('identityInput');
                        if (el) el.focus();
                    });
                },

                updateClock() {
                    const d = new Date();
                    this.clock = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
                },

                reset() {
                    clearTimeout(this.timer);
                    this.step = 'home';
                    this.visitorType = 'siswa';
                    this.guest = { name: '', origin: '', purpose: '' };
                    this.error = null;
                    this.loading = false;
                },

                autoReset(delay = 3000) {
                    clearTimeout(this.timer);
                    this.timer = setTimeout(() => this.reset(), delay);
                },

                async submitMember(manualCode = null) {
                    const inputEl = document.getElementById('identityInput');
                    const code = manualCode || (inputEl ? inputEl.value.trim() : '');
                    if (!code) {
                        this.error = 'Silakan scan atau ketik nomor identitas.';
                        return;
                    }

                    this.error = null;
                    this.loading = true;

                    try {
                        const res = await fetch('{{ route('kiosk.checkin') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                visitor_type: this.visitorType,
                                identity_number: code,
                            }),
                        });

                        const data = await res.json();

                        if (!data.ok) {
                            this.error = data.message;
                            this.loading = false;
                            return;
                        }

                        this.step = 'success';
                        this.successMessage = data.message;
                        this.successTime = data.visit_date;
                        this.loading = false;
                        this.autoReset(3000);
                    } catch (e) {
                        this.error = 'Gagal terhubung ke server.';
                        this.loading = false;
                    }
                },

                async submitGuest() {
                    if (!this.guest.name.trim() || !this.guest.origin.trim() || !this.guest.purpose) {
                        this.error = 'Lengkapi semua kolom yang wajib diisi.';
                        return;
                    }

                    this.error = null;
                    this.loading = true;

                    try {
                        const res = await fetch('{{ route('kiosk.checkin') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                visitor_type: 'tamu',
                                guest_name: this.guest.name,
                                guest_origin: this.guest.origin,
                                purpose: this.guest.purpose,
                            }),
                        });

                        const data = await res.json();

                        if (!data.ok) {
                            this.error = data.message;
                            this.loading = false;
                            return;
                        }

                        this.step = 'success';
                        this.successMessage = data.message;
                        this.successTime = data.visit_date;
                        this.loading = false;
                        this.autoReset(3000);
                    } catch (e) {
                        this.error = 'Gagal terhubung ke server.';
                        this.loading = false;
                    }
                }
            };
        }
    </script>
</x-layouts.kiosk>
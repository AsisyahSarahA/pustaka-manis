<x-layouts.kiosk>
    <div class="w-full max-w-4xl px-6 text-center" x-show="step === 'home'">
        {{-- Header --}}
        <div class="mb-8">
            @if ($logo = setting('app_logo'))
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center overflow-hidden rounded-5xl bg-azure-soft/10 shadow-[0_0_50px_rgba(151,221,233,0.35)]"><img src="{{ asset($logo) }}" alt="Logo" class="h-16 w-16 object-contain"></div>
            @else
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-5xl bg-azure-soft/15 text-azure-soft shadow-[0_0_50px_rgba(151,221,233,0.35)]"><x-icon name="landmark" class="h-10 w-10" /></div>
            @endif
            <h1 class="text-3xl font-bold text-pearl sm:text-4xl">SELAMAT DATANG DI</h1>
            <p class="mt-2 text-xl font-semibold text-azure-soft">PERPUSTAKAAN {{ strtoupper(setting('school_name', 'SMP')) }}</p>
            <p class="mt-1 text-sm text-pearl/50">Silakan pilih identitas Anda untuk mengisi buku tamu</p>
        </div>

        {{-- Tombol pilihan --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <button
                @click="step = 'form'; visitorType = 'siswa'"
                class="btn-skeuo glass-panel rounded-4xl p-8 transition-all duration-200 hover:-translate-y-1"
            >
                <span class="inline-flex h-16 w-16 items-center justify-center rounded-3xl bg-azure-soft/15 text-azure-soft"><x-icon name="graduation-cap" class="h-8 w-8" /></span>
                <p class="mt-3 text-lg font-bold text-pearl">SISWA</p>
                <p class="text-sm text-pearl/50">Scan kartu / NISN</p>
            </button>
            <button
                @click="step = 'form'; visitorType = 'guru'"
                class="btn-skeuo glass-panel rounded-4xl p-8 transition-all duration-200 hover:-translate-y-1"
            >
                <span class="inline-flex h-16 w-16 items-center justify-center rounded-3xl bg-white/10 text-pearl"><x-icon name="presentation" class="h-8 w-8" /></span>
                <p class="mt-3 text-lg font-bold text-pearl">GURU / STAF</p>
                <p class="text-sm text-pearl/50">Scan kartu / NIP</p>
            </button>
            <button
                @click="step = 'form'; visitorType = 'tamu'"
                class="btn-skeuo glass-panel rounded-4xl p-8 transition-all duration-200 hover:-translate-y-1"
            >
                <span class="inline-flex h-16 w-16 items-center justify-center rounded-3xl bg-amber-warm/20 text-amber-warm"><x-icon name="user-round" class="h-8 w-8" /></span>
                <p class="mt-3 text-lg font-bold text-pearl">TAMU</p>
                <p class="text-sm text-pearl/50">Kunjungan eksternal</p>
            </button>
        </div>

        {{-- Footer info --}}
        <div class="mt-10 flex items-center justify-center gap-8 text-sm text-pearl/50">
            <span class="inline-flex items-center gap-1.5"><x-icon name="calendar" class="h-4 w-4" /> {{ now()->translatedFormat('l, d F Y') }}</span>
            <span class="inline-flex items-center gap-1.5"><x-icon name="clock" class="h-4 w-4" /> <span x-text="clock"></span></span>
            <span class="inline-flex items-center gap-1.5"><x-icon name="users" class="h-4 w-4" /> Pengunjung hari ini: <span class="font-semibold text-azure-soft">{{ $todayVisitors }}</span></span>
        </div>
    </div>

    {{-- Form Siswa/Guru (Integrated Smart Scanner) --}}
    <div class="w-full max-w-lg px-6" x-show="step === 'form' && visitorType !== 'tamu'" x-cloak>
        <div class="glass-panel rounded-5xl p-8 text-center space-y-4">
            <span class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-3xl bg-azure-soft/15 text-azure-soft">
                <template x-if="visitorType === 'siswa'"><x-icon name="graduation-cap" class="h-8 w-8" /></template>
                <template x-if="visitorType !== 'siswa'"><x-icon name="presentation" class="h-8 w-8" /></template>
            </span>
            <h2 class="text-2xl font-bold text-pearl" x-text="visitorType === 'siswa' ? 'Check-in Kartu Siswa' : 'Check-in Kartu Guru / Staf'"></h2>

            <x-smart-scanner 
                name="kiosk_identity_input" 
                id="identityInput" 
                placeholder="Scan barcode / ketik NISN / NIP..." 
                label="Scan / Ketik Identitas"
            />

            <div class="mt-4 flex justify-center gap-3">
                <button @click="reset()" class="btn-skeuo rounded-pill bg-white/10 px-6 py-3 text-sm font-semibold text-pearl"><x-icon name="arrow-left" class="h-4 w-4" /> Kembali</button>
                <button @click="submitMember()" class="btn-skeuo rounded-pill bg-azure-soft px-8 py-3 text-sm font-bold text-navy-deep shadow-tactile">MASUK KUNJUNGAN ➔</button>
            </div>

            <p x-show="error" x-cloak class="mt-4 rounded-2xl bg-danger-soft/20 p-3 text-xs text-danger-red border border-danger-red/30" x-text="error"></p>
        </div>
    </div>

    {{-- Form Tamu --}}
    <div class="w-full max-w-lg px-6" x-show="step === 'form' && visitorType === 'tamu'" x-cloak>
        <div class="glass-panel rounded-5xl p-8">
            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-amber-warm/20 text-amber-warm"><x-icon name="user-round" class="h-8 w-8" /></span>
            <h2 class="mt-3 text-center text-2xl font-bold text-pearl">Form Tamu Eksternal</h2>
            <p class="mt-1 text-center text-sm text-pearl/50">Isi data diri untuk kunjungan Anda</p>

            <div class="mt-6 space-y-4">
                <input
                    type="text"
                    x-model="guest.name"
                    placeholder="Nama Lengkap *"
                    class="input-debossed w-full rounded-pill border-0 px-5 py-3 text-sm text-pearl"
                    autocomplete="off"
                />
                <input
                    type="text"
                    x-model="guest.origin"
                    placeholder="Instansi Asal *"
                    class="input-debossed w-full rounded-pill border-0 px-5 py-3 text-sm text-pearl"
                    autocomplete="off"
                />
                <select x-model="guest.purpose" class="input-debossed w-full rounded-pill border-0 px-5 py-3 text-sm text-pearl">
                    <option value="">Tujuan Kunjungan *</option>
                    <option value="penelitian">Penelitian</option>
                    <option value="studi banding">Studi Banding</option>
                    <option value="kunjungan resmi">Kunjungan Resmi</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div class="mt-6 flex justify-center gap-3">
                <button @click="reset()" class="btn-skeuo rounded-pill bg-white/10 px-6 py-3 text-sm font-semibold text-pearl"><x-icon name="arrow-left" class="h-4 w-4" /> Kembali</button>
                <button @click="submitGuest()" class="btn-skeuo rounded-pill bg-azure-soft px-8 py-3 text-sm font-bold text-navy-deep shadow-tactile">Submit Kunjungan</button>
            </div>

            <p x-show="error" x-cloak class="mt-4 rounded-2xl bg-danger-soft/20 p-3 text-xs text-danger-red border border-danger-red/30" x-text="error"></p>
        </div>
    </div>

    {{-- Sukses --}}
    <div class="w-full max-w-lg px-6" x-show="step === 'success'" x-cloak>
        <div class="glass-panel rounded-5xl p-12 text-center">
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-success-soft text-success-green shadow-[0_0_50px_rgba(151,221,233,0.5)]"><x-icon name="check" class="h-12 w-12" /></div>
            <h2 class="mt-6 text-2xl font-bold text-pearl" x-text="successMessage"></h2>
            <p class="mt-2 text-sm text-pearl/50" x-text="successTime"></p>
            <p class="mt-4 text-sm text-amber-warm">Terima kasih sudah berkunjung!</p>
        </div>
    </div>

    <script>
        function kioskApp() {
            return {
                step: 'home',
                visitorType: 'siswa',
                guest: { name: '', origin: '', purpose: '' },
                error: null,
                successMessage: '',
                successTime: '',
                clock: '',
                timer: null,
                clockTimer: null,

                init() {
                    this.updateClock();
                    this.clockTimer = setInterval(() => this.updateClock(), 1000);
                    document.addEventListener('keydown', this.handleKioskKey);

                    window.addEventListener('barcode-scanned', (e) => {
                        if (this.step === 'form' && this.visitorType !== 'tamu') {
                            this.submitMember(e.detail);
                        }
                    });
                },

                handleKioskKey(e) {
                    if (e.key === 'Escape') this.reset();
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
                            return;
                        }

                        this.step = 'success';
                        this.successMessage = data.message;
                        this.successTime = data.visit_date;
                        this.autoReset(3000);
                    } catch (e) {
                        this.error = 'Gagal terhubung ke server.';
                    }
                },

                async submitGuest() {
                    if (!this.guest.name.trim() || !this.guest.origin.trim() || !this.guest.purpose) {
                        this.error = 'Lengkapi semua kolom yang wajib diisi.';
                        return;
                    }

                    this.error = null;

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
                            return;
                        }

                        this.step = 'success';
                        this.successMessage = data.message;
                        this.successTime = data.visit_date;
                        this.autoReset(3000);
                    } catch (e) {
                        this.error = 'Gagal terhubung ke server.';
                    }
                }
            };
        }
    </script>
</x-layouts.kiosk>
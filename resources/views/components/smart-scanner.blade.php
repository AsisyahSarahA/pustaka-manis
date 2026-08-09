@props([
    'name' => 'barcode',
    'id' => 'barcode-input',
    'placeholder' => 'Scan / ketik kode barcode...',
    'label' => 'Kode Barcode',
    'autofocus' => true,
])

<div x-data="smartScanner({ targetId: '{{ $id }}' })" class="space-y-3">
    {{-- Header & Mode Toggle --}}
    <div class="flex items-center justify-between">
        <label for="{{ $id }}" class="block text-xs font-semibold uppercase tracking-wider text-pearl/70">
            {{ $label }}
        </label>
        <div class="inline-flex rounded-full bg-navy-soft/60 p-1 border border-white/10 backdrop-blur-md">
            <button
                type="button"
                @click="setMode('manual')"
                :class="mode === 'manual' ? 'bg-azure-soft text-navy-dark shadow-sm' : 'text-pearl/70 hover:text-pearl'"
                class="rounded-full px-3 py-1 text-xs font-semibold transition-all duration-200"
            >
                ⌨️ Manual / USB
            </button>
            <button
                type="button"
                @click="setMode('camera')"
                :class="mode === 'camera' ? 'bg-azure-soft text-navy-dark shadow-sm' : 'text-pearl/70 hover:text-pearl'"
                class="rounded-full px-3 py-1 text-xs font-semibold transition-all duration-200"
            >
                📷 Scan Kamera
            </button>
        </div>
    </div>

    {{-- Mode Manual / USB Scanner Input --}}
    <div x-show="mode === 'manual'" class="relative">
        <input
            type="text"
            id="{{ $id }}"
            name="{{ $name }}"
            x-model="code"
            x-ref="manualInput"
            @keydown.enter.prevent="$dispatch('barcode-scanned', code); $el.form && $el.form.requestSubmit ? $el.form.requestSubmit() : null"
            placeholder="{{ $placeholder }}"
            class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm text-pearl placeholder-pearl/40 focus:ring-2 focus:ring-azure-soft/50"
            @if($autofocus) autofocus @endif
        />
        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-pearl/40">
            [Enter / Auto-Scan]
        </div>
    </div>

    {{-- Mode Scan Kamera --}}
    <div x-show="mode === 'camera'" class="space-y-2" x-cloak>
        <div id="reader-{{ $id }}" class="overflow-hidden rounded-2xl border border-white/10 bg-black/40 text-center text-xs text-pearl/60 min-h-[220px] flex items-center justify-center">
            <div x-show="!isScanning" class="p-4">
                <p>Klik tombol di bawah untuk mengaktifkan kamera.</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button
                type="button"
                x-show="!isScanning"
                @click="startCameraScanner()"
                class="w-full rounded-full bg-azure-soft/20 border border-azure-soft/40 py-2.5 text-xs font-bold text-azure-soft hover:bg-azure-soft/30"
            >
                ▶️ Buka Kamera Device
            </button>
            <button
                type="button"
                x-show="isScanning"
                @click="stopCameraScanner()"
                class="w-full rounded-full bg-red-500/20 border border-red-500/40 py-2.5 text-xs font-bold text-red-300 hover:bg-red-500/30"
            >
                ⏹️ Hentikan Kamera
            </button>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('smartScanner', (config) => ({
                    mode: 'manual',
                    code: '',
                    isScanning: false,
                    html5QrcodeScanner: null,

                    setMode(newMode) {
                        this.mode = newMode;
                        if (newMode === 'manual') {
                            this.stopCameraScanner();
                            this.$nextTick(() => {
                                if (this.$refs.manualInput) {
                                    this.$refs.manualInput.focus();
                                }
                            });
                        } else {
                            this.startCameraScanner();
                        }
                    },

                    startCameraScanner() {
                        if (typeof Html5Qrcode === 'undefined') {
                            alert('Library html5-qrcode belum dimuat. Pastikan koneksi internet atau script CDN aktif.');
                            return;
                        }

                        const elementId = 'reader-' + config.targetId;
                        this.html5QrcodeScanner = new Html5Qrcode(elementId);
                        this.isScanning = true;

                        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                            this.code = decodedText;
                            this.$dispatch('barcode-scanned', decodedText);

                            // Audio Beep Feedback
                            this.playBeepSound();

                            const inputEl = document.getElementById(config.targetId);
                            if (inputEl) {
                                inputEl.value = decodedText;
                                inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                                if (inputEl.form) {
                                    inputEl.form.requestSubmit();
                                }
                            }
                            this.stopCameraScanner();
                            this.mode = 'manual';
                        };

                        const qrConfig = { fps: 10, qrbox: { width: 250, height: 150 } };

                        this.html5QrcodeScanner.start(
                            { facingMode: "environment" },
                            qrConfig,
                            qrCodeSuccessCallback
                        ).catch(err => {
                            console.warn("Camera start error:", err);
                            this.isScanning = false;
                        });
                    },

                    stopCameraScanner() {
                        if (this.html5QrcodeScanner && this.isScanning) {
                            this.html5QrcodeScanner.stop().then(() => {
                                this.html5QrcodeScanner.clear();
                                this.isScanning = false;
                            }).catch(err => {
                                console.warn("Camera stop error:", err);
                                this.isScanning = false;
                            });
                        }
                    },

                    playBeepSound() {
                        try {
                            const ctx = new (window.AudioContext || window.webkitAudioContext)();
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(880, ctx.currentTime); // 880 Hz pitch
                            gain.gain.setValueAtTime(0.1, ctx.currentTime);
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.start();
                            osc.stop(ctx.currentTime + 0.15);
                        } catch (e) {
                            // ignore audio context errors if blocked by browser policy
                        }
                    }
                }));
            });
        </script>
    @endpush
@endonce

@props([
    'name' => 'barcode',
    'id' => 'barcode-input',
    'placeholder' => 'Scan barcode / ketik kode...',
    'label' => 'Kode Barcode',
    'autofocus' => true,
])

<div x-data="smartScanner({ targetId: '{{ $id }}' })" class="space-y-3">
    {{-- Mode Toggle Header --}}
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-white/10 pb-2">
        <label for="{{ $id }}" class="text-xs font-bold uppercase tracking-wider text-pearl/80">
            {{ $label }}
        </label>
        <div class="inline-flex rounded-full bg-navy-dark/80 p-1 border border-white/10 backdrop-blur-md">
            <button
                type="button"
                @click="setMode('manual')"
                :class="mode === 'manual' ? 'bg-azure-soft text-navy-dark font-extrabold shadow-md' : 'text-pearl/70 hover:text-pearl font-medium'"
                class="rounded-full px-3.5 py-1.5 text-xs transition-all duration-200 cursor-pointer"
            >
                ⌨️ Input Manual / USB
            </button>
            <button
                type="button"
                @click="setMode('camera')"
                :class="mode === 'camera' ? 'bg-azure-soft text-navy-dark font-extrabold shadow-md' : 'text-pearl/70 hover:text-pearl font-medium'"
                class="rounded-full px-3.5 py-1.5 text-xs transition-all duration-200 cursor-pointer"
            >
                📷 Scan Kamera
            </button>
        </div>
    </div>

    {{-- Mode 1: Input Manual / USB Hardware Scanner --}}
    <div x-show="mode === 'manual'" class="relative">
        <div class="flex gap-2">
            <input
                type="text"
                id="{{ $id }}"
                name="{{ $name }}"
                x-model="code"
                x-ref="manualInput"
                @keydown.enter.prevent="handleEnter()"
                placeholder="{{ $placeholder }}"
                class="input-debossed w-full rounded-pill border border-white/10 px-5 py-3 text-sm text-pearl placeholder-pearl/40 focus:ring-2 focus:ring-azure-soft/50"
                @if($autofocus) autofocus @endif
            />
            <button
                type="button"
                @click="handleEnter()"
                class="rounded-pill bg-azure-soft px-5 py-3 text-xs font-bold text-navy-dark hover:bg-azure-glow transition shrink-0 cursor-pointer shadow-md active:scale-95"
            >
                Cari 🔍
            </button>
        </div>
        <p class="mt-1 text-[11px] text-pearl/50 px-2">
            💡 Ketik kode & tekan <kbd class="px-1.5 py-0.5 rounded bg-white/10 font-mono text-[10px] text-azure-soft">Enter</kbd> atau scan via USB Barcode Scanner.
        </p>
    </div>

    {{-- Mode 2: Scan Kamera & File Photo Barcode --}}
    <div x-show="mode === 'camera'" class="space-y-3" x-cloak>
        <div id="reader-{{ $id }}" class="overflow-hidden rounded-3xl border border-white/10 bg-black/60 text-center text-xs text-pearl/70 min-h-[220px] flex flex-col items-center justify-center p-4">
            <div x-show="!isScanning && !cameraError" class="space-y-2">
                <p class="text-sm font-semibold text-pearl">📷 Kamera Siap Digunakan</p>
                <p class="text-xs text-pearl/50">Klik tombol di bawah untuk mengaktifkan video kamera device.</p>
            </div>
            <div x-show="cameraError" class="text-red-300 p-3 text-xs space-y-1 bg-red-500/15 rounded-2xl border border-red-500/30">
                <p class="font-bold">⚠️ Kendala Kamera:</p>
                <p x-text="cameraError"></p>
                <p class="text-[10px] text-pearl/60">Anda dapat menggunakan opsi "Upload Foto Barcode" di bawah jika kamera browser diblokir.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <button
                type="button"
                x-show="!isScanning"
                @click="startCameraScanner()"
                class="rounded-full bg-azure-soft/20 border border-azure-soft/40 py-2.5 px-3 text-xs font-bold text-azure-soft hover:bg-azure-soft/30 transition text-center cursor-pointer active:scale-95 shadow-sm"
            >
                ▶️ Buka Kamera Device
            </button>
            <button
                type="button"
                x-show="isScanning"
                @click="stopCameraScanner()"
                class="rounded-full bg-red-500/20 border border-red-500/40 py-2.5 px-3 text-xs font-bold text-red-300 hover:bg-red-500/30 transition text-center cursor-pointer active:scale-95 shadow-sm"
            >
                ⏹️ Hentikan Kamera
            </button>
            <label class="cursor-pointer rounded-full bg-white/10 border border-white/20 py-2.5 px-3 text-center text-xs font-bold text-pearl hover:bg-white/20 transition active:scale-95 shadow-sm flex items-center justify-center gap-1">
                <span>📁 Upload Foto Barcode</span>
                <input type="file" accept="image/*" class="hidden" @change="scanFromFile($event)" />
            </label>
        </div>
    </div>
</div>

<script>
    if (typeof window.registerSmartScannerComponent === 'undefined') {
        window.registerSmartScannerComponent = function() {
            if (typeof window.Alpine === 'undefined') return;

            window.Alpine.data('smartScanner', (config) => ({
                mode: 'manual',
                code: '',
                isScanning: false,
                cameraError: null,
                html5QrcodeScanner: null,

                init() {
                    this.$nextTick(() => {
                        if (this.$refs.manualInput && this.mode === 'manual') {
                            this.$refs.manualInput.focus();
                        }
                    });
                },

                setMode(newMode) {
                    this.mode = newMode;
                    this.cameraError = null;
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

                handleEnter() {
                    const val = this.code.trim();
                    if (!val) return;
                    
                    // Dispatch target-specific and general events
                    this.$dispatch('scan-' + config.targetId, val);
                    this.$dispatch('barcode-scanned', val);
                    
                    const inputEl = document.getElementById(config.targetId);
                    if (inputEl) {
                        inputEl.value = val;
                        inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                        if (inputEl.form && typeof inputEl.form.requestSubmit === 'function') {
                            // Don't submit full form on book scan
                        }
                    }
                },

                async startCameraScanner() {
                    const QrLib = window.Html5Qrcode || (typeof Html5Qrcode !== 'undefined' ? Html5Qrcode : null);
                    if (!QrLib) {
                        this.cameraError = 'Library pemindai kamera sedang dimuat, silakan coba sesaat lagi atau gunakan input manual.';
                        return;
                    }

                    const elementId = 'reader-' + config.targetId;
                    this.cameraError = null;

                    if (this.html5QrcodeScanner) {
                        await this.stopCameraScanner();
                    }

                    this.html5QrcodeScanner = new QrLib(elementId);

                    const qrCodeSuccessCallback = (decodedText) => {
                        this.onBarcodeDetected(decodedText);
                    };

                    const qrConfig = { fps: 15, qrbox: { width: 260, height: 160 } };

                    try {
                        let cameraConstraint = { facingMode: "environment" };

                        try {
                            const devices = await QrLib.getCameras();
                            if (devices && devices.length > 0) {
                                const backCam = devices.find(d => 
                                    d.label.toLowerCase().includes('back') || 
                                    d.label.toLowerCase().includes('rear') ||
                                    d.label.toLowerCase().includes('environment')
                                );
                                cameraConstraint = backCam ? backCam.id : devices[0].id;
                            }
                        } catch (e) {}

                        await this.html5QrcodeScanner.start(
                            cameraConstraint,
                            qrConfig,
                            qrCodeSuccessCallback
                        );
                        this.isScanning = true;
                    } catch (err1) {
                        try {
                            await this.html5QrcodeScanner.start(
                                { facingMode: "user" },
                                qrConfig,
                                qrCodeSuccessCallback
                            );
                            this.isScanning = true;
                        } catch (err2) {
                            this.isScanning = false;
                            this.cameraError = 'Izin kamera ditolak atau kamera tidak aktif pada perangkat ini.';
                        }
                    }
                },

                async stopCameraScanner() {
                    if (this.html5QrcodeScanner && this.isScanning) {
                        try {
                            await this.html5QrcodeScanner.stop();
                            this.html5QrcodeScanner.clear();
                        } catch (e) {}
                    }
                    this.isScanning = false;
                },

                scanFromFile(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const QrLib = window.Html5Qrcode || (typeof Html5Qrcode !== 'undefined' ? Html5Qrcode : null);
                    if (!QrLib) {
                        alert('Library barcode scanner belum siap.');
                        return;
                    }

                    const elementId = 'reader-' + config.targetId;
                    const html5Qrcode = new QrLib(elementId);
                    
                    html5Qrcode.scanFile(file, true)
                        .then(decodedText => {
                            this.onBarcodeDetected(decodedText);
                        })
                        .catch(err => {
                            alert('Format foto barcode tidak terbaca. Pastikan foto jelas dan cukup terang.');
                        });
                },

                onBarcodeDetected(decodedText) {
                    this.code = decodedText;
                    this.$dispatch('scan-' + config.targetId, decodedText);
                    this.$dispatch('barcode-scanned', decodedText);
                    this.playBeepSound();

                    const inputEl = document.getElementById(config.targetId);
                    if (inputEl) {
                        inputEl.value = decodedText;
                        inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    this.stopCameraScanner();
                    this.mode = 'manual';
                },

                playBeepSound() {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(880, ctx.currentTime);
                        gain.gain.setValueAtTime(0.15, ctx.currentTime);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.15);
                    } catch (e) {}
                }
            }));
        };

        if (window.Alpine) {
            window.registerSmartScannerComponent();
        } else {
            document.addEventListener('alpine:init', window.registerSmartScannerComponent);
        }
    }
</script>

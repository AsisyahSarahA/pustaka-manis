<x-layouts.app>
    @section('page_title', 'Pengaturan')

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Logo Aplikasi --}}
        <div class="glass-panel rounded-4xl p-6">
            <h3 class="mb-5 flex items-center gap-2 font-bold text-pearl"><x-icon name="image" class="h-5 w-5 text-azure-soft" /> Logo Aplikasi</h3>
            <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-start">
                <div class="flex h-28 w-28 shrink-0 items-center justify-center overflow-hidden rounded-3xl bg-azure-soft/10 ring-1 ring-white/10">
                    @if ($logo = $settings['app_logo']->value ?? null)
                        <img src="{{ asset($logo) }}" alt="Logo aplikasi saat ini" class="h-full w-full object-contain p-2">
                    @else
                        <x-icon name="library" class="h-12 w-12 text-azure-soft" />
                    @endif
                </div>
                <div class="flex-1">
                    <label class="mb-2 block text-sm font-medium text-pearl/80">Unggah Foto Logo</label>
                    <input
                        type="file"
                        name="app_logo"
                        accept="image/png,image/jpeg,image/jpg"
                        class="block w-full text-sm text-pearl/70 file:mr-3 file:rounded-pill file:border-0 file:bg-azure-soft file:px-4 file:py-2 file:text-sm file:font-semibold file:text-navy-deep hover:file:bg-azure-soft/90"
                    />
                    <p class="mt-2 text-xs text-pearl/50">Format PNG / JPG, maksimal 2 MB. Logo otomatis tampil di sidebar, halaman login, kiosk, dan kop surat laporan.</p>
                    <div id="logo-preview" class="mt-3 hidden"></div>
                </div>
            </div>
        </div>

        {{-- Profil Sekolah --}}
        <div class="glass-panel rounded-4xl p-6">
            <h3 class="mb-5 font-bold text-pearl">🏫 Profil Sekolah</h3>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-input name="app_name" label="Nama Aplikasi" :value="old('app_name', $settings['app_name']->value ?? 'PustakaManis')" required />
                <x-input name="school_name" label="Nama Sekolah" :value="old('school_name', $settings['school_name']->value ?? 'SMP Negeri 1')" required />
                <div class="md:col-span-2">
                    <x-input name="school_address" label="Alamat Sekolah" :value="old('school_address', $settings['school_address']->value ?? '')" />
                </div>
            </div>
        </div>

        {{-- Aturan Peminjaman --}}
        <div class="glass-panel rounded-4xl p-6">
            <h3 class="mb-5 flex items-center gap-2 font-bold text-pearl"><x-icon name="clock" class="h-5 w-5 text-azure-soft" /> Aturan Peminjaman</h3>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                <x-input type="number" name="loan_days_siswa" label="Durasi Pinjam Siswa (hari)" :value="old('loan_days_siswa', $settings['loan_days_siswa']->value ?? 7)" />
                <x-input type="number" name="loan_days_guru" label="Durasi Pinjam Guru (hari)" :value="old('loan_days_guru', $settings['loan_days_guru']->value ?? 14)" />
                <x-input type="number" name="loan_days_staf" label="Durasi Pinjam Staf (hari)" :value="old('loan_days_staf', $settings['loan_days_staf']->value ?? 14)" />
                <x-input type="number" name="max_loan_siswa" label="Kuota Pinjam Siswa" :value="old('max_loan_siswa', $settings['max_loan_siswa']->value ?? 2)" />
                <x-input type="number" name="max_loan_guru" label="Kuota Pinjam Guru" :value="old('max_loan_guru', $settings['max_loan_guru']->value ?? 5)" />
                <x-input type="number" name="max_loan_staf" label="Kuota Pinjam Staf" :value="old('max_loan_staf', $settings['max_loan_staf']->value ?? 3)" />
            </div>
        </div>

        {{-- Denda --}}
        <div class="glass-panel rounded-4xl p-6">
            <h3 class="mb-5 font-bold text-pearl">💰 Denda</h3>
            <label class="mb-5 flex items-center gap-2 text-sm text-pearl/70">
                <input type="checkbox" name="fine_enabled" value="1" @checked(($settings['fine_enabled']->value ?? 'true') === 'true') class="rounded border-white/20 bg-white/10 text-azure-soft focus:ring-azure-soft">
                Aktifkan fitur denda
            </label>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-input type="number" name="fine_per_day" label="Denda per Hari (Rp)" :value="old('fine_per_day', $settings['fine_per_day']->value ?? 500)" />
                <x-input type="number" name="fine_max_days" label="Maksimal Hari Denda Dihitung" :value="old('fine_max_days', $settings['fine_max_days']->value ?? 30)" />
            </div>
        </div>

        {{-- Module Toggles --}}
        <div class="glass-panel rounded-4xl p-6">
            <h3 class="mb-5 font-bold text-pearl">🔌 Modul Aplikasi</h3>
            <p class="mb-4 text-sm text-pearl/50">Nonaktifkan modul untuk menyembunyikan menu terkait dari navigasi.</p>
            <div class="space-y-3">
                @foreach ([
                    'module_visitor_enabled' => 'Buku Tamu & Kiosk',
                    'module_report_enabled' => 'Laporan',
                    'module_fine_enabled' => 'Denda',
                    'module_member_card_enabled' => 'Cetak Kartu Anggota',
                ] as $key => $label)
                    <label class="flex cursor-pointer items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-5 py-4">
                        <span class="text-sm font-medium text-pearl">{{ $label }}</span>
                        <input type="hidden" name="{{ $key }}" value="0" />
                        <input type="checkbox" name="{{ $key }}" value="1"
                            @checked(($settings[$key]->value ?? 'true') === 'true')
                            class="peer sr-only" />
                        <span class="relative h-7 w-12 rounded-pill bg-white/15 transition-colors duration-200 peer-checked:bg-azure-soft">
                            <span class="absolute left-1 top-1 h-5 w-5 rounded-full bg-pearl shadow transition-transform duration-200 peer-checked:translate-x-5"></span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <x-button type="submit" variant="primary" class="px-10 py-3">Simpan Pengaturan</x-button>
        </div>
    </form>

    <script>
        document.querySelector('input[name="app_logo"]')?.addEventListener('change', function () {
            const preview = document.getElementById('logo-preview');
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Pratinjau logo" class="mx-auto h-24 w-24 rounded-3xl bg-azure-soft/10 object-contain p-2 ring-1 ring-white/10">';
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</x-layouts.app>
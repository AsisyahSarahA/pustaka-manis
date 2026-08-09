<x-layouts.app>
    @section('page_title', 'Impor Buku')

    <div class="mx-auto max-w-2xl">
        <div class="glass-panel rounded-4xl p-6">
            <h2 class="text-lg font-bold text-pearl">Impor Buku Massal</h2>
            <p class="mt-1 text-sm text-pearl/50">
                Unggah file CSV atau XLSX. Unduh template untuk melihat format yang benar.
            </p>

            <a href="{{ route('books.template') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-azure-soft hover:underline">
                <x-icon name="download" class="h-4 w-4" /> Unduh Template CSV
            </a>

            <form method="POST" action="{{ route('books.import.process') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                @csrf

                <div class="rounded-3xl border-2 border-dashed border-white/15 p-8 text-center transition hover:border-azure-soft/40">
                    <label for="file" class="cursor-pointer">
                        <span class="text-4xl">📥</span>
                        <p class="mt-3 font-medium text-pearl">Klik untuk memilih file</p>
                        <p class="text-sm text-pearl/40">CSV / XLSX maksimal 5MB</p>
                        <input type="file" name="file" id="file" accept=".csv,.txt,.xlsx,.xls" class="hidden" required>
                    </label>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-pearl/60">
                    <p class="mb-1 font-semibold text-pearl/80">Format kolom wajib:</p>
                    <code class="font-mono text-xs text-azure-soft">judul, penulis, penerbit, tahun_terbit, kategori, jumlah_eksemplar, lokasi_rak</code>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <x-button href="{{ route('books.index') }}" variant="secondary">Batal</x-button>
                    <x-button type="submit" variant="primary">Mulai Impor</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

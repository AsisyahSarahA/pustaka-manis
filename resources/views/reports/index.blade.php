<x-layouts.app>
    @section('page_title', 'Laporan')

    <div class="space-y-6" x-data="{ periodType: '{{ $period_type }}' }">
        {{-- Header & Export Actions --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-black uppercase text-black font-heading tracking-tight">Laporan Perpustakaan</h2>
                <p class="mt-1 text-sm font-medium text-black/70">Ekspor laporan berkala untuk keperluan evaluasi, akreditasi, dan audit dinas.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-button href="{{ route('reports.export', array_merge(request()->query(), ['type' => $type, 'format' => 'pdf'])) }}" variant="danger" class="shadow-brutal">
                    <span class="h-4 w-4"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'/></svg></span>
                    PDF Formal
                </x-button>
                <x-button href="{{ route('reports.export', array_merge(request()->query(), ['type' => $type, 'format' => 'excel'])) }}" variant="primary" class="shadow-brutal">
                    <span class="h-4 w-4"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M3.375 19.5h17.25m-17.25-3h17.25m-17.25-3h17.25m-17.25-3h17.25M6.75 6.75h10.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25V9a2.25 2.25 0 012.25-2.25z'/></svg></span>
                    Excel (.xlsx)
                </x-button>
                @if ($type === 'monthly' || $type === 'circulation')
                    <x-button href="{{ route('reports.export', array_merge(request()->query(), ['type' => $type, 'format' => 'word'])) }}" variant="blue" class="shadow-brutal">
                        <span class="h-4 w-4"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5-3h7.5M12 3v5.25c0 .621.504 1.125 1.125 1.125h5.25'/></svg></span>
                        Word (.docx)
                    </x-button>
                @endif
            </div>
        </div>

        {{-- Tabs Jenis Laporan --}}
        <div class="glass-panel flex flex-wrap gap-2 p-3">
            @php
                $tabs = [
                    'monthly' => 'Sirkulasi Ringkasan',
                    'loans' => 'Peminjaman',
                    'overdue' => 'Keterlambatan',
                    'inventory' => 'Inventaris Buku',
                    'visitors' => 'Kunjungan Buku Tamu',
                ];
            @endphp
            @foreach ($tabs as $key => $label)
                <a
                    href="{{ route('reports.index', array_merge(request()->except(['type']), ['type' => $key])) }}"
                    class="border-2 border-black px-4 py-2 text-xs font-black uppercase tracking-wider transition-transform duration-75 {{ $type === $key ? 'bg-brutal-yellow text-black shadow-brutal-sm -translate-y-0.5' : 'bg-white text-black hover:bg-brutal-input' }}"
                >{{ $label }}</a>
            @endforeach
        </div>

        {{-- Form Filter Periode (Mingguan, Bulanan, Tahunan, Kustom) --}}
        <form method="GET" class="glass-panel p-6 space-y-5">
            <input type="hidden" name="type" value="{{ $type }}" />

            {{-- Period Mode Selector Tabs --}}
            @if ($type !== 'inventory')
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b-2 border-black pb-4">
                    <label class="text-xs font-black uppercase tracking-wider text-black flex items-center gap-2 font-heading">
                        <span>⏱️</span> Pilih Rentang Waktu Laporan:
                    </label>

                    <div class="inline-flex border-2 border-black bg-brutal-input p-1 shadow-brutal-sm gap-1">
                        <label class="cursor-pointer border-2 px-3 py-1.5 text-xs font-black uppercase tracking-wider transition" :class="periodType === 'weekly' ? 'border-black bg-brutal-neon text-black shadow-brutal-sm' : 'border-transparent text-black/70 hover:text-black'">
                            <input type="radio" name="period_type" value="weekly" x-model="periodType" class="hidden" />
                            📅 Per Minggu
                        </label>
                        <label class="cursor-pointer border-2 px-3 py-1.5 text-xs font-black uppercase tracking-wider transition" :class="periodType === 'monthly' ? 'border-black bg-brutal-neon text-black shadow-brutal-sm' : 'border-transparent text-black/70 hover:text-black'">
                            <input type="radio" name="period_type" value="monthly" x-model="periodType" class="hidden" />
                            🗓️ Per Bulan
                        </label>
                        <label class="cursor-pointer border-2 px-3 py-1.5 text-xs font-black uppercase tracking-wider transition" :class="periodType === 'yearly' ? 'border-black bg-brutal-neon text-black shadow-brutal-sm' : 'border-transparent text-black/70 hover:text-black'">
                            <input type="radio" name="period_type" value="yearly" x-model="periodType" class="hidden" />
                            📆 Per Tahun
                        </label>
                        <label class="cursor-pointer border-2 px-3 py-1.5 text-xs font-black uppercase tracking-wider transition" :class="periodType === 'custom' ? 'border-black bg-brutal-neon text-black shadow-brutal-sm' : 'border-transparent text-black/70 hover:text-black'">
                            <input type="radio" name="period_type" value="custom" x-model="periodType" class="hidden" />
                            ⏳ Rentang Kustom
                        </label>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4 items-end">
                {{-- 1. Input Khusus Mode Mingguan --}}
                <template x-if="periodType === 'weekly' && '{{ $type }}' !== 'inventory'">
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase text-black">Minggu Ke-</label>
                        <select name="week" class="input-debossed w-full px-4 py-3 text-sm font-bold">
                            <option value="1" @selected($week == 1)>Minggu 1 (Tgl 1 - 7)</option>
                            <option value="2" @selected($week == 2)>Minggu 2 (Tgl 8 - 14)</option>
                            <option value="3" @selected($week == 3)>Minggu 3 (Tgl 15 - 21)</option>
                            <option value="4" @selected($week == 4)>Minggu 4 (Tgl 22 - 28)</option>
                            <option value="5" @selected($week == 5)>Minggu 5 (Tgl 29 - Akhir)</option>
                        </select>
                    </div>
                </template>

                {{-- 2. Input Bulan (Untuk Mingguan & Bulanan) --}}
                <template x-if="(periodType === 'weekly' || periodType === 'monthly') && '{{ $type }}' !== 'inventory'">
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase text-black">Bulan</label>
                        <select name="month" class="input-debossed w-full px-4 py-3 text-sm font-bold">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" @selected($month == $m)>{{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                </template>

                {{-- 3. Input Tahun (Untuk Mingguan, Bulanan, Tahunan) --}}
                <template x-if="periodType !== 'custom' && '{{ $type }}' !== 'inventory'">
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase text-black">Tahun</label>
                        <select name="year" class="input-debossed w-full px-4 py-3 text-sm font-bold">
                            @foreach (range(now()->year, now()->year - 5) as $y)
                                <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </template>

                {{-- 4. Input Rentang Kustom (Dari & Sampai) --}}
                <template x-if="periodType === 'custom' && '{{ $type }}' !== 'inventory'">
                    <div class="col-span-1 md:col-span-2 grid grid-cols-2 gap-3">
                        <x-input type="date" name="start" label="Dari Tanggal" :value="$start" />
                        <x-input type="date" name="end" label="Sampai Tanggal" :value="$end" />
                    </div>
                </template>

                {{-- Filter Khusus Tipe Laporan --}}
                @if ($type === 'inventory')
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase text-black">Kategori Buku</label>
                        <select name="category_id" class="input-debossed w-full px-4 py-3 text-sm font-bold">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected($category_id == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase text-black">Status Eksemplar</label>
                        <select name="status" class="input-debossed w-full px-4 py-3 text-sm font-bold">
                            <option value="">Semua Status</option>
                            @foreach (['tersedia' => 'Tersedia', 'dipinjam' => 'Dipinjam', 'rusak' => 'Rusak', 'hilang' => 'Hilang'] as $val => $label)
                                <option value="{{ $val }}" @selected($status == $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @elseif ($type === 'visitors')
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase text-black">Tipe Pengunjung</label>
                        <select name="visitor_type" class="input-debossed w-full px-4 py-3 text-sm font-bold">
                            <option value="">Semua Tipe Pengunjung</option>
                            @foreach (['siswa' => 'Siswa', 'guru' => 'Guru / Karyawan', 'tamu' => 'Tamu Luar'] as $val => $label)
                                <option value="{{ $val }}" @selected($visitor_type == $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @elseif ($type === 'loans' || $type === 'overdue')
                    <div>
                        <x-input name="class" label="Filter Kelas / Jabatan" :value="$class" placeholder="mis. VII-A / Guru" />
                    </div>
                @endif

                {{-- Tombol Filter --}}
                <div class="flex gap-2">
                    <x-button type="submit" variant="amber" class="flex-1 py-3 text-xs font-black shadow-brutal">
                        🔍 TERAPKAN FILTER
                    </x-button>
                    <x-button href="{{ route('reports.index', ['type' => $type]) }}" variant="secondary" class="py-3 px-4 text-xs font-black shadow-brutal" title="Reset ke Bulan Ini">
                        🔄
                    </x-button>
                </div>
            </div>
        </form>

        {{-- Active Period Info Banner --}}
        <div class="border-3 border-black bg-brutal-yellow p-4 shadow-brutal flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs font-bold text-black">
            <div class="flex items-center gap-2">
                <span class="text-lg">📌</span>
                <span><strong>PERIODE AKTIF:</strong> <span class="bg-black text-white px-2 py-0.5 font-mono">{{ $period_label }}</span> ({{ \Carbon\Carbon::parse($start)->format('d/m/Y') }} s.d. {{ \Carbon\Carbon::parse($end)->format('d/m/Y') }})</span>
            </div>
            <div class="font-mono text-[11px] text-black/70">
                Update: {{ now()->translatedFormat('d F Y H:i') }}
            </div>
        </div>

        {{-- Konten Data Laporan --}}
        @if ($type === 'inventory')
            @include('reports.partials.inventory-table', ['books' => $books])
        @elseif ($type === 'visitors')
            @include('reports.partials.visitors-table', ['visitors' => $visitors])
        @elseif ($type === 'monthly' || $type === 'circulation')
            @include('reports.partials.monthly-table', [
                'loans' => $loans, 'returns' => $returns, 'visitors' => $visitors,
                'totalFine' => $totalFine, 'totalBorrowed' => $totalBorrowed, 'overdue' => $overdue,
            ])
        @else
            @include('reports.partials.loans-table', ['loans' => $loans, 'overdue' => $type === 'overdue'])
        @endif
    </div>
</x-layouts.app>
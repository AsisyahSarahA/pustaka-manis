<x-layouts.app>
    @section('page_title', 'Laporan')

    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-pearl">Laporan Perpustakaan</h2>
                <p class="mt-1 text-sm text-pearl/50">Ekspor laporan berkala untuk keperluan audit dan evaluasi.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-button href="{{ route('reports.export', array_merge(request()->query(), ['type' => $type, 'format' => 'pdf'])) }}" variant="secondary" class="border-red-500/30 text-red-300 hover:bg-red-500/10">
                    <span class="h-4 w-4"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'/></svg></span>
                    PDF Formal
                </x-button>
                <x-button href="{{ route('reports.export', array_merge(request()->query(), ['type' => $type, 'format' => 'excel'])) }}" variant="secondary" class="border-emerald-500/30 text-emerald-300 hover:bg-emerald-500/10">
                    <span class="h-4 w-4"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M3.375 19.5h17.25m-17.25-3h17.25m-17.25-3h17.25m-17.25-3h17.25M6.75 6.75h10.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25V9a2.25 2.25 0 012.25-2.25z'/></svg></span>
                    Excel (.xlsx)
                </x-button>
                @if ($type === 'monthly')
                    <x-button href="{{ route('reports.export', array_merge(request()->query(), ['type' => $type, 'format' => 'word'])) }}" variant="primary" class="border-blue-500/30 text-blue-300 hover:bg-blue-500/10">
                        <span class="h-4 w-4"><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5-3h7.5M12 3v5.25c0 .621.504 1.125 1.125 1.125h5.25'/></svg></span>
                        Word (.docx)
                    </x-button>
                @endif
            </div>
        </div>

        {{-- Tabs jenis laporan --}}
        <div class="glass-panel flex flex-wrap gap-2 rounded-4xl p-3">
            @php
                $tabs = [
                    'loans' => 'Peminjaman',
                    'overdue' => 'Keterlambatan',
                    'inventory' => 'Inventaris',
                    'visitors' => 'Kunjungan',
                    'monthly' => 'Sirkulasi Bulanan',
                ];
            @endphp
            @foreach ($tabs as $key => $label)
                <a
                    href="{{ route('reports.index', array_merge(request()->except(['type']), ['type' => $key])) }}"
                    class="rounded-pill px-4 py-2 text-sm font-medium transition-all duration-200 {{ $type === $key ? 'bg-azure-soft/15 text-azure-soft shadow-[0_0_16px_rgba(151,221,233,0.15)]' : 'text-pearl/60 hover:bg-white/5 hover:text-pearl' }}"
                >{{ $label }}</a>
            @endforeach
        </div>

        {{-- Form filter --}}
        <form method="GET" class="glass-panel rounded-4xl p-4">
            <input type="hidden" name="type" value="{{ $type }}" />
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                @if ($type === 'inventory')
                    <select name="category_id" class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($category_id == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm">
                        <option value="">Semua Status</option>
                        @foreach (['tersedia' => 'Tersedia', 'dipinjam' => 'Dipinjam', 'perbaikan' => 'Dalam Perbaikan'] as $val => $label)
                            <option value="{{ $val }}" @selected($status == $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                @elseif ($type === 'monthly')
                    <select name="month" class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm">
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" @selected($month == $m)>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                        @endforeach
                    </select>
                    <select name="year" class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm">
                        @foreach (range(now()->year, now()->year - 5) as $y)
                            <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                @elseif ($type === 'visitors')
                    <x-input type="date" name="start" label="Dari" :value="$start" />
                    <x-input type="date" name="end" label="Sampai" :value="$end" />
                    <select name="visitor_type" class="input-debossed w-full rounded-pill border-0 px-4 py-3 text-sm">
                        <option value="">Semua Tipe</option>
                        @foreach (['siswa' => 'Siswa', 'guru' => 'Guru', 'tamu' => 'Tamu'] as $val => $label)
                            <option value="{{ $val }}" @selected($visitor_type == $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                @else
                    <x-input type="date" name="start" label="Dari" :value="$start" />
                    <x-input type="date" name="end" label="Sampai" :value="$end" />
                    <x-input name="class" label="Kelas / Jabatan (opsional)" :value="$class" placeholder="mis. VII-A" />
                @endif
                <div class="flex items-end">
                    <x-button type="submit" variant="primary" class="w-full">Terapkan Filter</x-button>
                </div>
            </div>
        </form>

        {{-- Konten per tipe --}}
        @if ($type === 'inventory')
            @include('reports.partials.inventory-table', ['books' => $books])
        @elseif ($type === 'visitors')
            @include('reports.partials.visitors-table', ['visitors' => $visitors])
        @elseif ($type === 'monthly')
            @include('reports.partials.monthly-table', [
                'loans' => $loans, 'returns' => $returns, 'visitors' => $visitors,
                'totalFine' => $totalFine, 'totalBorrowed' => $totalBorrowed, 'overdue' => $overdue,
            ])
        @else
            @include('reports.partials.loans-table', ['loans' => $loans, 'overdue' => $type === 'overdue'])
        @endif
    </div>
</x-layouts.app>
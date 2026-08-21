<x-layouts.app>
    @section('page_title', 'Dashboard')

    <div class="space-y-6" x-data="dashboardAnalytics()" x-init="initCharts()">
        {{-- Sapaan kontekstual --}}
        <div class="glass-panel p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black uppercase text-black font-heading tracking-tight">
                        @if (now()->hour < 11)
                            Selamat pagi,
                        @elseif (now()->hour < 15)
                            Selamat siang,
                        @else
                            Selamat sore,
                        @endif
                        {{ Auth::user()->name }}!
                    </h2>
                    <p class="mt-1 text-sm font-medium text-black/70">
                        Sistem Informasi & Sirkulasi Perpustakaan {{ setting('school_name', 'SMP') }} siap melayani.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 border-2 border-black bg-brutal-yellow px-4 py-2 text-xs font-black uppercase tracking-wider shadow-brutal-sm">
                    <span>⚡ LIVE SIRKULASI</span>
                </div>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-5">
            <div class="glass-panel p-5 transition-transform duration-75 hover:-translate-y-1 hover:shadow-brutal-lg">
                <div class="flex items-center justify-between">
                    <span class="inline-flex h-10 w-10 items-center justify-center border-2 border-black bg-brutal-blue text-white shadow-brutal-sm"><x-icon name="library" class="h-5 w-5" /></span>
                    <span class="font-mono text-xs font-bold text-black/50">TOTAL</span>
                </div>
                <p class="mt-4 font-mono text-4xl font-black text-black">{{ $totalBooks }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-black/70 mt-1">Judul Buku</p>
            </div>

            <div class="glass-panel p-5 transition-transform duration-75 hover:-translate-y-1 hover:shadow-brutal-lg">
                <div class="flex items-center justify-between">
                    <span class="inline-flex h-10 w-10 items-center justify-center border-2 border-black bg-brutal-neon text-black shadow-brutal-sm"><x-icon name="circle-check" class="h-5 w-5" /></span>
                    <span class="font-mono text-xs font-bold text-black/50">STOK</span>
                </div>
                <p class="mt-4 font-mono text-4xl font-black text-black">{{ $availableBooks }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-black/70 mt-1">Buku Tersedia</p>
            </div>

            <div class="glass-panel p-5 transition-transform duration-75 hover:-translate-y-1 hover:shadow-brutal-lg">
                <div class="flex items-center justify-between">
                    <span class="inline-flex h-10 w-10 items-center justify-center border-2 border-black bg-brutal-blue text-white shadow-brutal-sm"><x-icon name="book-open" class="h-5 w-5" /></span>
                    <span class="font-mono text-xs font-bold text-black/50">AKTIF</span>
                </div>
                <p class="mt-4 font-mono text-4xl font-black text-black">{{ $borrowedBooks }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-black/70 mt-1">Buku Dipinjam</p>
            </div>

            <div class="glass-panel p-5 transition-transform duration-75 hover:-translate-y-1 hover:shadow-brutal-lg {{ $overdueCount > 0 ? 'bg-brutal-pink/10' : '' }}">
                <div class="flex items-center justify-between">
                    <span class="inline-flex h-10 w-10 items-center justify-center border-2 border-black bg-brutal-pink text-white shadow-brutal-sm"><x-icon name="triangle-alert" class="h-5 w-5" /></span>
                    <span class="font-mono text-xs font-bold text-black/50">PERINGATAN</span>
                </div>
                <p class="mt-4 font-mono text-4xl font-black {{ $overdueCount > 0 ? 'text-brutal-pink' : 'text-black' }}">{{ $overdueCount }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-black/70 mt-1">Keterlambatan</p>
            </div>

            <div class="glass-panel p-5 transition-transform duration-75 hover:-translate-y-1 hover:shadow-brutal-lg">
                <div class="flex items-center justify-between">
                    <span class="inline-flex h-10 w-10 items-center justify-center border-2 border-black bg-brutal-yellow text-black shadow-brutal-sm"><x-icon name="users" class="h-5 w-5" /></span>
                    <span class="font-mono text-xs font-bold text-black/50">HARI INI</span>
                </div>
                <p class="mt-4 font-mono text-4xl font-black text-black">{{ $todayVisitors }}</p>
                <p class="text-xs font-bold uppercase tracking-wider text-black/70 mt-1">Pengunjung</p>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="glass-panel p-6">
            <h3 class="mb-4 text-sm font-black uppercase tracking-wider text-black font-heading">Aksi Cepat Sirkulasi</h3>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <x-button href="{{ route('loans.borrow') }}" variant="primary" class="py-4 text-xs font-black shadow-brutal">
                    <x-icon name="book-open" class="h-4 w-4" /> PINJAM BUKU
                </x-button>
                <x-button href="{{ route('loans.return') }}" variant="secondary" class="py-4 text-xs font-black shadow-brutal">
                    <x-icon name="inbox" class="h-4 w-4" /> KEMBALIKAN
                </x-button>
                <x-button href="{{ route('books.create') }}" variant="yellow" class="py-4 text-xs font-black shadow-brutal">
                    <x-icon name="plus" class="h-4 w-4" /> TAMBAH BUKU
                </x-button>
                <x-button href="{{ route('kiosk.index') }}" target="_blank" variant="secondary" class="py-4 text-xs font-black shadow-brutal">
                    <x-icon name="landmark" class="h-4 w-4" /> KIOSK BUKU TAMU
                </x-button>
            </div>
        </div>

        {{-- Grafik Sirkulasi 7 Hari --}}
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            {{-- Tren Peminjaman 7 Hari --}}
            <div class="glass-panel p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b-2 border-black pb-3 mb-4">
                        <div>
                            <h3 class="font-black text-sm uppercase tracking-wider text-black font-heading flex items-center gap-2">
                                <span>📈</span> Tren Peminjaman 7 Hari
                            </h3>
                            <p class="text-xs font-medium text-black/60 mt-0.5">Transaksi peminjaman buku per hari</p>
                        </div>
                        <span class="border-2 border-black bg-brutal-neon px-3 py-1 text-xs font-black uppercase tracking-wider shadow-brutal-sm">
                            Total: {{ $loanTrend['total_7_days'] }} Buku
                        </span>
                    </div>

                    {{-- Metric Info Cards --}}
                    <div class="grid grid-cols-3 gap-3 mb-4 text-center">
                        <div class="border-2 border-black bg-brutal-input p-2.5 shadow-brutal-sm">
                            <p class="text-[10px] font-black uppercase text-black/60">Total 7 Hari</p>
                            <p class="text-lg font-black font-mono text-black">{{ $loanTrend['total_7_days'] }}</p>
                        </div>
                        <div class="border-2 border-black bg-brutal-input p-2.5 shadow-brutal-sm">
                            <p class="text-[10px] font-black uppercase text-black/60">Rata-rata/Hari</p>
                            <p class="text-lg font-black font-mono text-black">{{ $loanTrend['avg_per_day'] }}</p>
                        </div>
                        <div class="border-2 border-black bg-brutal-yellow p-2.5 shadow-brutal-sm">
                            <p class="text-[10px] font-black uppercase text-black">Hari Puncak</p>
                            <p class="text-xs font-black font-mono text-black mt-1 truncate" title="{{ $loanTrend['peak_day'] }}">{{ $loanTrend['peak_day'] }}</p>
                        </div>
                    </div>

                    <div class="h-60 relative">
                        <canvas id="loanChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Tren Kunjungan 7 Hari --}}
            <div class="glass-panel p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b-2 border-black pb-3 mb-4">
                        <div>
                            <h3 class="font-black text-sm uppercase tracking-wider text-black font-heading flex items-center gap-2">
                                <span>👥</span> Tren Kunjungan 7 Hari
                            </h3>
                            <p class="text-xs font-medium text-black/60 mt-0.5">Pengunjung perpustakaan (siswa, guru, tamu)</p>
                        </div>
                        <span class="border-2 border-black bg-brutal-yellow px-3 py-1 text-xs font-black uppercase tracking-wider shadow-brutal-sm">
                            Total: {{ $visitTrend['total_7_days'] }} Orang
                        </span>
                    </div>

                    {{-- Metric Info Cards --}}
                    <div class="grid grid-cols-3 gap-3 mb-4 text-center">
                        <div class="border-2 border-black bg-brutal-input p-2.5 shadow-brutal-sm">
                            <p class="text-[10px] font-black uppercase text-black/60">Total 7 Hari</p>
                            <p class="text-lg font-black font-mono text-black">{{ $visitTrend['total_7_days'] }}</p>
                        </div>
                        <div class="border-2 border-black bg-brutal-input p-2.5 shadow-brutal-sm">
                            <p class="text-[10px] font-black uppercase text-black/60">Rata-rata/Hari</p>
                            <p class="text-lg font-black font-mono text-black">{{ $visitTrend['avg_per_day'] }}</p>
                        </div>
                        <div class="border-2 border-black bg-brutal-neon p-2.5 shadow-brutal-sm">
                            <p class="text-[10px] font-black uppercase text-black">Hari Tersibuk</p>
                            <p class="text-xs font-black font-mono text-black mt-1 truncate" title="{{ $visitTrend['peak_day'] }}">{{ $visitTrend['peak_day'] }}</p>
                        </div>
                    </div>

                    <div class="h-60 relative">
                        <canvas id="visitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kategori Terpopuler --}}
        <div class="glass-panel p-6">
            <div class="flex items-center justify-between border-b-2 border-black pb-3 mb-4">
                <div>
                    <h3 class="font-black text-sm uppercase tracking-wider text-black font-heading flex items-center gap-2">
                        <span>🏆</span> Kategori Buku Terpopuler
                    </h3>
                    <p class="text-xs font-medium text-black/60 mt-0.5">Distribusi peminjaman berdasarkan kategori koleksi buku</p>
                </div>
                <span class="border-2 border-black bg-white px-3 py-1 text-xs font-black uppercase tracking-wider shadow-brutal-sm">
                    Total Sirkulasi: {{ $topCategories['total_all'] }} Buku
                </span>
            </div>

            @if(count($topCategories['labels']) === 0)
                <div class="border-2 border-dashed border-black bg-brutal-input p-10 text-center">
                    <p class="text-3xl">📚</p>
                    <p class="mt-2 text-sm font-black uppercase text-black font-heading">Belum Ada Data Peminjaman</p>
                    <p class="text-xs font-medium text-black/60 mt-1">Data kategori terpopuler otomatis terisi saat transaksi peminjaman berlangsung.</p>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
                    <div class="h-64 relative flex items-center justify-center">
                        <canvas id="categoryChart"></canvas>
                    </div>

                    <div class="space-y-3">
                        <p class="text-xs font-black uppercase tracking-wider text-black border-b-2 border-black pb-2 font-heading">
                            Peringkat 5 Kategori Terbanyak Dipinjam
                        </p>
                        @php
                            $badgeColors = ['#FFDE00', '#00FF66', '#0066FF', '#FF003C', '#EAEAE5'];
                        @endphp
                        @foreach($topCategories['items'] as $index => $item)
                            <div class="border-2 border-black bg-white p-3 shadow-brutal-sm flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center border-2 border-black text-xs font-black font-mono {{ $index === 2 ? 'text-white' : 'text-black' }}" style="background-color: {{ $badgeColors[$index % 5] }}">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="font-bold text-sm text-black uppercase tracking-tight truncate">{{ $item['name'] }}</span>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="text-xs font-mono font-bold text-black/70">{{ $item['total'] }} buku</span>
                                    <span class="border-2 border-black bg-brutal-yellow px-2 py-0.5 text-xs font-black font-mono text-black">
                                        {{ $item['percentage'] }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function dashboardAnalytics() {
            return {
                loanChartInstance: null,
                visitChartInstance: null,
                categoryChartInstance: null,

                initCharts() {
                    this.$nextTick(() => {
                        this.renderCharts();
                    });

                    document.addEventListener('page:loaded', () => {
                        this.renderCharts();
                    });
                },

                renderCharts() {
                    const ChartLib = window.Chart || (typeof Chart !== 'undefined' ? Chart : null);
                    if (!ChartLib) {
                        setTimeout(() => this.renderCharts(), 200);
                        return;
                    }

                    const gridColor = 'rgba(0, 0, 0, 0.08)';
                    const tickColor = '#000000';
                    const fontConfig = { family: 'Space Grotesk, sans-serif', weight: 'bold', size: 11 };

                    // 1. Line: Tren Peminjaman
                    const loanEl = document.getElementById('loanChart');
                    if (loanEl) {
                        if (this.loanChartInstance) this.loanChartInstance.destroy();
                        this.loanChartInstance = new ChartLib(loanEl, {
                            type: 'line',
                            data: {
                                labels: @json($loanTrend['labels']),
                                datasets: [{
                                    label: 'Peminjaman',
                                    data: @json($loanTrend['data']),
                                    borderColor: '#0066FF',
                                    backgroundColor: 'rgba(0, 102, 255, 0.12)',
                                    fill: true,
                                    tension: 0.2,
                                    pointRadius: 6,
                                    pointHoverRadius: 8,
                                    pointBackgroundColor: '#FFDE00',
                                    pointBorderColor: '#000000',
                                    pointBorderWidth: 2,
                                    borderWidth: 3,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: '#000000',
                                        titleColor: '#FFDE00',
                                        titleFont: { weight: 'bold', size: 12 },
                                        bodyColor: '#FFFFFF',
                                        bodyFont: { weight: 'bold', size: 11 },
                                        borderColor: '#000000',
                                        borderWidth: 2,
                                        padding: 10,
                                        cornerRadius: 0,
                                        callbacks: {
                                            label: (ctx) => ` ${ctx.parsed.y} Buku Dipinjam`
                                        }
                                    }
                                },
                                scales: {
                                    x: { grid: { color: gridColor }, ticks: { color: tickColor, font: fontConfig } },
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: gridColor },
                                        ticks: { color: tickColor, precision: 0, font: fontConfig }
                                    }
                                }
                            }
                        });
                    }

                    // 2. Bar: Tren Kunjungan
                    const visitEl = document.getElementById('visitChart');
                    if (visitEl) {
                        if (this.visitChartInstance) this.visitChartInstance.destroy();
                        this.visitChartInstance = new ChartLib(visitEl, {
                            type: 'bar',
                            data: {
                                labels: @json($visitTrend['labels']),
                                datasets: [{
                                    label: 'Kunjungan',
                                    data: @json($visitTrend['data']),
                                    backgroundColor: '#FFDE00',
                                    borderColor: '#000000',
                                    borderWidth: 2,
                                    borderRadius: 0,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: '#000000',
                                        titleColor: '#00FF66',
                                        titleFont: { weight: 'bold', size: 12 },
                                        bodyColor: '#FFFFFF',
                                        bodyFont: { weight: 'bold', size: 11 },
                                        borderColor: '#000000',
                                        borderWidth: 2,
                                        padding: 10,
                                        cornerRadius: 0,
                                        callbacks: {
                                            label: (ctx) => ` ${ctx.parsed.y} Pengunjung`
                                        }
                                    }
                                },
                                scales: {
                                    x: { grid: { color: gridColor }, ticks: { color: tickColor, font: fontConfig } },
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: gridColor },
                                        ticks: { color: tickColor, precision: 0, font: fontConfig }
                                    }
                                }
                            }
                        });
                    }

                    // 3. Doughnut: Kategori Terpopuler
                    const catEl = document.getElementById('categoryChart');
                    if (catEl && @json(count($topCategories['labels'])) > 0) {
                        if (this.categoryChartInstance) this.categoryChartInstance.destroy();
                        this.categoryChartInstance = new ChartLib(catEl, {
                            type: 'doughnut',
                            data: {
                                labels: @json($topCategories['labels']),
                                datasets: [{
                                    data: @json($topCategories['data']),
                                    backgroundColor: ['#FFDE00', '#00FF66', '#0066FF', '#FF003C', '#EAEAE5'],
                                    borderColor: '#000000',
                                    borderWidth: 3,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: { color: tickColor, font: fontConfig, boxWidth: 15, padding: 12 }
                                    },
                                    tooltip: {
                                        backgroundColor: '#000000',
                                        titleColor: '#FFDE00',
                                        bodyColor: '#FFFFFF',
                                        borderColor: '#000000',
                                        borderWidth: 2,
                                        padding: 10,
                                        cornerRadius: 0,
                                        callbacks: {
                                            label: (ctx) => ` ${ctx.label}: ${ctx.parsed} Buku`
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            };
        }
    </script>
</x-layouts.app>
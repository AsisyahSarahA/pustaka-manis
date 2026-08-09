<x-layouts.app>
    @section('page_title', 'Dashboard')

    <div class="space-y-6">
        {{-- Sapaan kontekstual --}}
        <div class="glass-panel rounded-4xl p-6">
            <h2 class="text-xl font-bold text-pearl">
                @if (now()->hour < 11)
                    Selamat pagi,
                @elseif (now()->hour < 15)
                    Selamat siang,
                @else
                    Selamat sore,
                @endif
                {{ Auth::user()->name }}!
            </h2>
            <p class="mt-1 text-sm text-pearl/50">
                Perpustakaan {{ setting('school_name', 'SMP') }} siap melayani. Berikut ringkasan sirkulasi.
            </p>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-5">
            <div class="glass-panel rounded-4xl p-5 transition-transform duration-200 hover:-translate-y-1">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-azure-soft/15 text-azure-soft"><x-icon name="library" class="h-5 w-5" /></span>
                <p class="mt-3 text-3xl font-bold text-pearl">{{ $totalBooks }}</p>
                <p class="text-sm text-pearl/50">Total Judul Buku</p>
            </div>
            <div class="glass-panel rounded-4xl p-5 transition-transform duration-200 hover:-translate-y-1">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-success-soft text-success-green"><x-icon name="circle-check" class="h-5 w-5" /></span>
                <p class="mt-3 text-3xl font-bold text-success-green">{{ $availableBooks }}</p>
                <p class="text-sm text-pearl/50">Buku Tersedia</p>
            </div>
            <div class="glass-panel rounded-4xl p-5 transition-transform duration-200 hover:-translate-y-1">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-azure-soft"><x-icon name="book-open" class="h-5 w-5" /></span>
                <p class="mt-3 text-3xl font-bold text-azure-soft">{{ $borrowedBooks }}</p>
                <p class="text-sm text-pearl/50">Buku Dipinjam</p>
            </div>
            <div class="glass-panel rounded-4xl p-5 transition-transform duration-200 hover:-translate-y-1">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-danger-soft text-danger-red"><x-icon name="triangle-alert" class="h-5 w-5" /></span>
                <p class="mt-3 text-3xl font-bold {{ $overdueCount > 0 ? 'text-danger-red' : 'text-pearl' }}">{{ $overdueCount }}</p>
                <p class="text-sm text-pearl/50">Keterlambatan</p>
            </div>
            <div class="glass-panel rounded-4xl p-5 transition-transform duration-200 hover:-translate-y-1">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-warm/20 text-amber-warm"><x-icon name="users" class="h-5 w-5" /></span>
                <p class="mt-3 text-3xl font-bold text-amber-warm">{{ $todayVisitors }}</p>
                <p class="text-sm text-pearl/50">Kunjungan Hari Ini</p>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="glass-panel rounded-4xl p-6">
            <h3 class="mb-4 font-bold text-pearl">Aksi Cepat</h3>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <x-button href="{{ route('loans.borrow') }}" variant="primary" class="py-4"><x-icon name="book-open" class="h-4 w-4" /> Pinjam Buku</x-button>
                <x-button href="{{ route('loans.return') }}" variant="secondary" class="py-4"><x-icon name="inbox" class="h-4 w-4" /> Kembalikan</x-button>
                <x-button href="{{ route('books.create') }}" variant="secondary" class="py-4"><x-icon name="plus" class="h-4 w-4" /> Tambah Buku</x-button>
                <x-button href="{{ route('kiosk.index') }}" target="_blank" variant="secondary" class="py-4"><x-icon name="landmark" class="h-4 w-4" /> Kiosk Buku Tamu</x-button>
            </div>
        </div>

        {{-- Grafik --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            <div class="glass-panel rounded-4xl p-6">
                <h3 class="mb-4 font-bold text-pearl">Tren Peminjaman 7 Hari</h3>
                <div class="h-64">
                    <canvas id="loanChart"></canvas>
                </div>
            </div>
            <div class="glass-panel rounded-4xl p-6">
                <h3 class="mb-4 font-bold text-pearl">Tren Kunjungan 7 Hari</h3>
                <div class="h-64">
                    <canvas id="visitChart"></canvas>
                </div>
            </div>
        </div>

        <div class="glass-panel rounded-4xl p-6">
            <h3 class="mb-4 font-bold text-pearl">Kategori Terpopuler</h3>
            <div class="h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart.js dimuat terpisah (hanya di halaman ini) agar aplikasi tetap ringan --}}
    @push('scripts')
        @vite(['resources/js/charts.js'])
    @endpush

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chartData = @json($loanTrend);

            const gridColor = 'rgba(255,255,255,0.08)';
            const tickColor = 'rgba(244,247,246,0.5)';
            const azure = '#97DDE9';
            const azureLight = '#CFEBFF';

            Chart.defaults.color = tickColor;
            Chart.defaults.borderColor = gridColor;

            // Line: Tren Peminjaman
            new Chart(document.getElementById('loanChart'), {
                type: 'line',
                data: {
                    labels: @json($loanTrend['labels']),
                    datasets: [{
                        label: 'Peminjaman',
                        data: @json($loanTrend['data']),
                        borderColor: azure,
                        backgroundColor: (ctx) => {
                            const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 260);
                            g.addColorStop(0, 'rgba(151,221,233,0.4)');
                            g.addColorStop(1, 'rgba(151,221,233,0)');
                            return g;
                        },
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: azureLight,
                        pointBorderColor: azure,
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });

            // Bar: Tren Kunjungan
            new Chart(document.getElementById('visitChart'), {
                type: 'bar',
                data: {
                    labels: @json($visitTrend['labels']),
                    datasets: [{
                        label: 'Kunjungan',
                        data: @json($visitTrend['data']),
                        backgroundColor: 'rgba(151,221,233,0.55)',
                        borderColor: azure,
                        borderWidth: 1,
                        borderRadius: 8,
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });

            // Doughnut: Kategori terpopuler
            const top = @json($topCategories);
            new Chart(document.getElementById('categoryChart'), {
                type: 'doughnut',
                data: {
                    labels: top.labels,
                    datasets: [{
                        data: top.data,
                        backgroundColor: ['#97DDE9', '#CFEBFF', '#FBBF24', '#F87171', '#8b9dc3'],
                        borderColor: '#162032',
                        borderWidth: 3,
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: tickColor } } } }
            });
        });
    </script>
</x-layouts.app>
@extends('reports.pdf.layout')

@section('content')
    {{-- Ringkasan Eksekutif --}}
    <table class="summary-grid">
        <tr>
            <td style="width: 33%;">
                <div class="summary-card">
                    <div class="val">{{ number_format(count($loans)) }}</div>
                    <div class="lbl">Total Transaksi</div>
                </div>
            </td>
            <td style="width: 33%;">
                <div class="summary-card">
                    <div class="val" style="color: #2563eb;">{{ number_format($loans->sum(fn($l) => $l->items->count())) }}</div>
                    <div class="lbl">Total Buku Dipinjam</div>
                </div>
            </td>
            <td style="width: 34%;">
                <div class="summary-card">
                    <div class="val" style="color: #16a34a;">{{ number_format($loans->where('status', 'selesai')->count()) }}</div>
                    <div class="lbl">Transaksi Selesai</div>
                </div>
            </td>
        </tr>
    </table>

    <h3 style="font-size: 11px; margin: 10px 0 6px; color: #0f172a; font-weight: bold; text-transform: uppercase;">Detail Transaksi Peminjaman Buku</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Kode Pinjam</th>
                <th style="width: 25%;">Nama Peminjam</th>
                <th style="width: 15%;">Kategori / Kelas</th>
                <th style="width: 13%; text-align: center;">Tgl Pinjam</th>
                <th style="width: 13%; text-align: center;">Jt Tempo</th>
                <th style="width: 14%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($loans as $index => $loan)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-weight: bold; font-family: monospace;">{{ $loan->loan_code }}</td>
                    <td style="font-weight: 600; color: #0f172a;">{{ $loan->member->name ?? '-' }}</td>
                    <td>{{ ucfirst($loan->member->type ?? 'Siswa') }} {{ $loan->member->department_class ? '('.$loan->member->department_class.')' : '' }}</td>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($loan->borrow_date)->format('d/m/Y') }}</td>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y') }}</td>
                    <td style="text-align: center; font-weight: bold;">
                        @if($loan->status === 'selesai')
                            <span style="color: #16a34a;">Selesai</span>
                        @elseif($loan->status === 'terlambat' || (\Carbon\Carbon::parse($loan->due_date)->isPast() && $loan->status === 'berjalan'))
                            <span style="color: #dc2626;">Terlambat</span>
                        @else
                            <span style="color: #2563eb;">Berjalan</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b; padding: 15px;">Tidak ada transaksi peminjaman pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" style="text-align: right; text-transform: uppercase;">Total Akumulasi Transaksi:</td>
                <td style="text-align: center;">{{ count($loans) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
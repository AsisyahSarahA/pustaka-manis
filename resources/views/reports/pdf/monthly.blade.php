@extends('reports.pdf.layout')

@section('content')
    {{-- Ringkasan Eksekutif --}}
    <table class="summary-grid">
        <tr>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="val">{{ number_format($total_borrowed_items ?? 0) }}</div>
                    <div class="lbl">Total Pinjam</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="val">{{ number_format($returns_count ?? 0) }}</div>
                    <div class="lbl">Total Kembali</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="val">{{ number_format($visitors_count ?? 0) }}</div>
                    <div class="lbl">Pengunjung</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="val">{{ number_format($overdue_count ?? 0) }}</div>
                    <div class="lbl">Terlambat</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="val">Rp {{ number_format($total_fine_amount ?? 0, 0, ',', '.') }}</div>
                    <div class="lbl">Total Denda</div>
                </div>
            </td>
        </tr>
    </table>

    <h3 style="font-size: 11px; margin: 10px 0 6px; color: #0f172a; font-weight: bold; text-transform: uppercase;">Detail Sirkulasi Peminjaman</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Kode Pinjam</th>
                <th style="width: 22%;">Nama Anggota</th>
                <th style="width: 13%;">Kategori</th>
                <th style="width: 12%; text-align: center;">Tgl Pinjam</th>
                <th style="width: 12%; text-align: center;">Jt. Tempo</th>
                <th style="width: 11%; text-align: center;">Status</th>
                <th style="width: 10%; text-align: center;">Jml Buku</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($loans as $index => $loan)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-weight: bold; font-family: monospace;">{{ $loan->loan_code }}</td>
                    <td>{{ $loan->member->name ?? '-' }}</td>
                    <td>{{ ucfirst($loan->member->type ?? 'siswa') }} {{ $loan->member->department_class ? '('.$loan->member->department_class.')' : '' }}</td>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($loan->borrow_date)->format('d/m/Y') }}</td>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y') }}</td>
                    <td style="text-align: center;">
                        @if($loan->status === 'selesai')
                            <span style="color: #16a34a; font-weight: bold;">Selesai</span>
                        @elseif($loan->status === 'terlambat' || (\Carbon\Carbon::parse($loan->due_date)->isPast() && $loan->status === 'berjalan'))
                            <span style="color: #dc2626; font-weight: bold;">Terlambat</span>
                        @else
                            <span style="color: #2563eb; font-weight: bold;">Berjalan</span>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $loan->items->count() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #64748b; padding: 15px;">Tidak ada transaksi sirkulasi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" style="text-align: right; text-transform: uppercase;">Total Transaksi Peminjaman:</td>
                <td style="text-align: center;">{{ count($loans) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
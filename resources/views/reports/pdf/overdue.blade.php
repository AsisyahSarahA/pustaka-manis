@extends('reports.pdf.layout')

@section('content')
    {{-- Ringkasan Eksekutif --}}
    <table class="summary-grid">
        <tr>
            <td style="width: 50%;">
                <div class="summary-card">
                    <div class="val" style="color: #dc2626;">{{ number_format(count($loans)) }}</div>
                    <div class="lbl">Total Pinjaman Terlambat</div>
                </div>
            </td>
            <td style="width: 50%;">
                <div class="summary-card">
                    <div class="val" style="color: #d97706;">{{ number_format($loans->sum(fn($l) => $l->items_count ?? 0)) }}</div>
                    <div class="lbl">Total Eksemplar Terlibat</div>
                </div>
            </td>
        </tr>
    </table>

    <h3 style="font-size: 11px; margin: 10px 0 6px; color: #0f172a; font-weight: bold; text-transform: uppercase;">Daftar Transaksi Keterlambatan Peminjaman</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Kode Pinjam</th>
                <th style="width: 25%;">Nama Anggota</th>
                <th style="width: 18%;">Kategori / Kelas</th>
                <th style="width: 12%; text-align: center;">Tgl Pinjam</th>
                <th style="width: 12%; text-align: center;">Jt Tempo</th>
                <th style="width: 13%; text-align: center;">Keterlambatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($loans as $index => $loan)
                @php
                    $daysLate = max(0, \Carbon\Carbon::parse($loan->due_date)->diffInDays(now()));
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-weight: bold; font-family: monospace;">{{ $loan->loan_code }}</td>
                    <td style="font-weight: 600; color: #0f172a;">{{ $loan->member->name ?? '-' }}</td>
                    <td>{{ ucfirst($loan->member->type ?? 'Siswa') }} {{ $loan->member->department_class ? '('.$loan->member->department_class.')' : '' }}</td>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($loan->borrow_date)->format('d/m/Y') }}</td>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y') }}</td>
                    <td style="text-align: center; font-weight: bold; color: #dc2626;">{{ $daysLate }} Hari</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b; padding: 15px;">Tidak ada transaksi keterlambatan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" style="text-align: right; text-transform: uppercase;">Total Transaksi Terlambat:</td>
                <td style="text-align: center; color: #dc2626;">{{ count($loans) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
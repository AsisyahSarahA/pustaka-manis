@extends('reports.pdf.layout')

@section('content')
    {{-- Ringkasan Eksekutif --}}
    <table class="summary-grid">
        <tr>
            <td style="width: 25%;">
                <div class="summary-card">
                    <div class="val">{{ number_format(count($visitors)) }}</div>
                    <div class="lbl">Total Kunjungan</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-card">
                    <div class="val" style="color: #2563eb;">{{ number_format($visitors->where('visitor_type', 'siswa')->count()) }}</div>
                    <div class="lbl">Siswa</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-card">
                    <div class="val" style="color: #0284c7;">{{ number_format($visitors->where('visitor_type', 'guru')->count()) }}</div>
                    <div class="lbl">Guru / Staf</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-card">
                    <div class="val" style="color: #d97706;">{{ number_format($visitors->where('visitor_type', 'tamu')->count()) }}</div>
                    <div class="lbl">Tamu Eksternal</div>
                </div>
            </td>
        </tr>
    </table>

    <h3 style="font-size: 11px; margin: 10px 0 6px; color: #0f172a; font-weight: bold; text-transform: uppercase;">Daftar Presensi Kunjungan Buku Tamu</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Waktu Kunjungan</th>
                <th style="width: 12%;">Tipe</th>
                <th style="width: 28%;">Nama Pengunjung</th>
                <th style="width: 20%;">Kelas / Instansi</th>
                <th style="width: 20%;">Tujuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($visitors as $index => $visitor)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($visitor->visit_date)->format('d/m/Y') }} {{ $visitor->check_in_time ? '('.$visitor->check_in_time.')' : '' }}</td>
                    <td style="font-weight: bold;">{{ ucfirst($visitor->visitor_type ?? 'Siswa') }}</td>
                    <td style="font-weight: 600; color: #0f172a;">{{ $visitor->member->name ?? $visitor->guest_name }}</td>
                    <td>{{ $visitor->member->department_class ?? $visitor->guest_origin ?? '-' }}</td>
                    <td>{{ $visitor->purpose ?? 'Kunjungan Perpustakaan' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b; padding: 15px;">Tidak ada catatan kunjungan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right; text-transform: uppercase;">Total Akumulasi Pengunjung:</td>
                <td style="text-align: center;">{{ count($visitors) }} Orang</td>
            </tr>
        </tfoot>
    </table>
@endsection
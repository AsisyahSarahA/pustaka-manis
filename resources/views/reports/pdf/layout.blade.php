<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $report_title ?? 'Laporan Perpustakaan' }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        /* Kop Surat Formal */
        .kop-container {
            position: relative;
            text-align: center;
            padding-bottom: 8px;
            margin-bottom: 15px;
            border-bottom: 3px double #0f172a;
        }
        .kop-container h2 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-container h1 {
            margin: 2px 0;
            font-size: 16px;
            font-weight: 800;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-container p {
            margin: 1px 0;
            font-size: 9px;
            color: #475569;
        }
        .kop-subtext {
            font-size: 8px;
            color: #64748b;
            margin-top: 3px;
        }
        
        /* Judul Laporan */
        .report-header {
            text-align: center;
            margin-bottom: 15px;
        }
        .report-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .report-subtitle {
            font-size: 10px;
            color: #475569;
            margin-top: 3px;
        }

        /* Ringkasan Eksekutif */
        .summary-grid {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }
        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
        }
        .summary-card .val {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }
        .summary-card .lbl {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
            margin-top: 2px;
        }

        /* Tabel Data Rapi Soft Navy */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #0f172a;
            text-align: left;
        }
        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            font-size: 9px;
            color: #334155;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        table.data-table tr.total-row {
            background-color: #e2e8f0;
            font-weight: bold;
        }
        table.data-table tr.total-row td {
            border-top: 2px solid #0f172a;
            color: #0f172a;
        }

        /* Utility Page Break */
        .page-break {
            page-break-after: always;
        }
        tr {
            page-break-inside: avoid;
        }
        .keep-together {
            page-break-inside: avoid;
        }

        /* Format Tanda Tangan */
        .signature-container {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            border: none;
            vertical-align: top;
            text-align: center;
            padding: 0 10px;
        }
        .signature-title {
            font-size: 9.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 50px;
        }
        .signature-name {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
        }
        .signature-nip {
            font-size: 8.5px;
            color: #64748b;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 15px;
            text-align: right;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 3px;
        }
    </style>
</head>
<body>
    {{-- Kop Surat Resmi Sekolah --}}
    <div class="kop-container">
        @if ($logo = setting('app_logo'))
            <div style="display: flex; justify-content: center; margin-bottom: 6px;">
                <img src="{{ public_path($logo) }}" style="height: 48px; max-width: 120px; object-fit: contain;" />
            </div>
        @endif
        <h2>PEMERINTAH KABUPATEN / KOTA</h2>
        <h1>DINAS PENDIDIKAN DAN KEBUDAYAAN</h1>
        <h2>{{ strtoupper(setting('school_name', 'SMP NEGERI 1 PUSTAKA')) }}</h2>
        <p>{{ setting('school_address', 'Jl. Pendidikan No. 45 Telp. (021) 555-0199') }}</p>
        <p class="kop-subtext">Email: {{ setting('school_email', 'info@sekolah.sch.id') }} | Website: {{ setting('school_website', 'www.sekolah.sch.id') }}</p>
    </div>

    {{-- Judul Laporan --}}
    <div class="report-header">
        <div class="report-title">{{ $report_title ?? 'LAPORAN SIRKULASI' }}</div>
        <div class="report-subtitle">
            @if (isset($month_name))
                Periode: {{ $month_name }}
            @elseif(isset($start) && isset($end))
                Periode: {{ \Carbon\Carbon::parse($start)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($end)->translatedFormat('d F Y') }}
            @else
                Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
            @endif
        </div>
    </div>

    @yield('content')

    {{-- Blok Tanda Tangan Resmi --}}
    <div class="signature-container">
        <table class="signature-table">
            <tr>
                <td style="width: 50%;">
                    <div>&nbsp;</div>
                    <div class="signature-title">Mengetahui,<br>Kepala Sekolah</div>
                    <div class="signature-name">{{ setting('headmaster_name', 'Drs. H. Ahmad Dahlan, M.Pd') }}</div>
                    <div class="signature-nip">NIP. {{ setting('headmaster_nip', '19680512 199403 1 004') }}</div>
                </td>
                <td style="width: 50%;">
                    <div>{{ setting('school_city', 'Kota Pustaka') }}, {{ now()->translatedFormat('d F Y') }}</div>
                    <div class="signature-title">Kepala / Pustakawan Utama</div>
                    <div class="signature-name">{{ setting('librarian_name', 'Nurhayati, S.IP') }}</div>
                    <div class="signature-nip">NIP. {{ setting('librarian_nip', '19820914 200801 2 011') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dokumen Resmi {{ setting('app_name', 'PustakaManis') }} — Halaman 1
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk Denda - {{ $fine->receipt_number ?? 'Denda #'.$fine->id }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            background: #f1f5f9;
            margin: 0;
            padding: 10px;
            display: flex;
            justify-content: center;
        }
        .no-print {
            position: fixed;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 8px;
            z-index: 100;
        }
        .btn-action {
            background: #0f172a;
            color: white;
            border: none;
            padding: 8px 16px;
            font-family: sans-serif;
            font-size: 12px;
            font-weight: bold;
            border-radius: 20px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        .receipt-card {
            width: 76mm;
            background: #fff;
            padding: 12px 10px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            position: relative;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .dashed-line {
            border-bottom: 1px dashed #000;
            margin: 8px 0;
        }
        .double-line {
            border-bottom: 2px double #000;
            margin: 8px 0;
        }

        /* Skeuomorphic LUNAS Stamp Effect */
        .stamp-lunas {
            position: absolute;
            top: 40%;
            right: 10px;
            transform: rotate(-15deg);
            border: 3px double #dc2626;
            color: #dc2626;
            font-family: 'Arial Black', sans-serif;
            font-size: 18pt;
            font-weight: 900;
            padding: 4px 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-radius: 6px;
            opacity: 0.85;
            box-shadow: 0 0 0 2px #fff, 0 0 10px rgba(220, 38, 38, 0.2);
            pointer-events: none;
            text-shadow: 1px 1px 0px rgba(220,38,38,0.3);
        }

        .stamp-waived {
            position: absolute;
            top: 40%;
            right: 10px;
            transform: rotate(-15deg);
            border: 3px double #2563eb;
            color: #2563eb;
            font-family: 'Arial Black', sans-serif;
            font-size: 14pt;
            font-weight: 900;
            padding: 4px 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 6px;
            opacity: 0.85;
            pointer-events: none;
        }

        .barcode-box {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .receipt-card { border: none; box-shadow: none; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-action">🖨️ Cetak Struk (Thermal/A4)</button>
        <button onclick="window.close()" class="btn-action" style="background:#475569;">Tutup</button>
    </div>

    <div class="receipt-card">
        {{-- Stamp Graphics --}}
        @if ($fine->status === 'paid')
            <div class="stamp-lunas">LUNAS</div>
        @elseif($fine->status === 'waived')
            <div class="stamp-waived">DIMAAFKAN</div>
        @endif

        <div class="text-center font-bold" style="font-size: 13px;">
            PERPUSTAKAAN<br>
            {{ strtoupper(setting('school_name', 'SMP NEGERI 1 PUSTAKA')) }}
        </div>
        <div class="text-center" style="font-size: 9px; margin-top: 2px;">
            {{ setting('school_address', 'Jl. Pendidikan No. 45') }}
        </div>
        
        <div class="double-line"></div>

        <div class="text-center font-bold" style="font-size: 11px;">
            @if ($fine->status === 'paid')
                BUKTI PELUNASAN DENDA
            @elseif($fine->status === 'waived')
                SURAT KETERANGAN BEBAS DENDA
            @else
                STRUK TUNGGAKAN DENDA
            @endif
        </div>
        
        <div class="dashed-line"></div>

        <table style="width: 100%; font-size: 10px; border-collapse: collapse;">
            <tr>
                <td>No. Struk</td>
                <td>: {{ $fine->receipt_number ?? '#FIN-'.$fine->id }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ $fine->payment_date ? \Carbon\Carbon::parse($fine->payment_date)->format('d/m/Y H:i') : \Carbon\Carbon::parse($fine->fine_date)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Peminjam</td>
                <td>: {{ $fine->member->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>No. Induk</td>
                <td>: {{ $fine->member->member_code ?? '-' }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>: {{ $fine->user->name ?? 'System' }}</td>
            </tr>
        </table>

        <div class="dashed-line"></div>

        <div style="font-size: 10px;">
            <div class="font-bold">Buku Terlambat:</div>
            <div>{{ $fine->loanItem->bookItem->book->title ?? 'Peminjaman Buku Terlambat' }}</div>
            <div style="font-size: 9px; opacity: 0.8;">[Code: {{ $fine->loanItem->bookItem->item_code ?? '-' }}]</div>
        </div>

        <div class="dashed-line"></div>

        <table style="width: 100%; font-size: 11px;" class="font-bold">
            <tr>
                <td>TOTAL DENDA</td>
                <td class="text-right">Rp {{ number_format($fine->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>STATUS</td>
                <td class="text-right" style="text-transform: uppercase;">{{ $fine->status }}</td>
            </tr>
        </table>

        @if ($fine->notes)
            <div style="font-size: 9px; margin-top: 5px; font-style: italic;">
                Catatan: {{ $fine->notes }}
            </div>
        @endif

        <div class="double-line"></div>

        {{-- Barcode Pembayaran jika unpaid --}}
        @if ($fine->status === 'unpaid')
            <div class="barcode-box">
                {!! DNS1D::getBarcodeSVG('FIN-'.$fine->id, 'C128', 1.0, 24, 'black') !!}
                <div style="font-size: 8px; margin-top: 2px;">Serahkan barcode ini ke kasir untuk bayar</div>
            </div>
        @endif

        <div class="text-center" style="font-size: 9px; margin-top: 10px;">
            Simpan struk ini sebagai bukti pembayaran resmi.<br>
            Terima kasih atas kerjasamanya.
        </div>
    </div>
</body>
</html>

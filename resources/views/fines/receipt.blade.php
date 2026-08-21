<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk Denda - {{ $fine->receipt_number ?? 'Denda #'.$fine->id }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&display=swap');
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'JetBrains Mono', 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            background: #F4F4F0;
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
            background: #FFDE00;
            color: #000;
            border: 3px solid #000;
            padding: 8px 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 4px 4px 0px 0px #000;
        }
        .btn-action:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0px 0px #000; }
        .receipt-card {
            width: 76mm;
            background: #fff;
            padding: 14px 12px;
            border: 3px solid #000;
            box-shadow: 6px 6px 0px 0px #000;
            position: relative;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 800; }
        .ascii-line {
            font-weight: 700;
            letter-spacing: -1px;
            overflow: hidden;
            white-space: nowrap;
            margin: 6px 0;
            text-align: center;
        }

        /* Brutalist LUNAS Stamp Effect */
        .stamp-lunas {
            position: absolute;
            top: 35%;
            right: 10px;
            transform: rotate(-12deg);
            border: 4px solid #FF003C;
            background: #FFDE00;
            color: #FF003C;
            font-family: 'JetBrains Mono', monospace;
            font-size: 16pt;
            font-weight: 900;
            padding: 4px 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 4px 4px 0px 0px #000;
            pointer-events: none;
        }

        .stamp-waived {
            position: absolute;
            top: 35%;
            right: 10px;
            transform: rotate(-12deg);
            border: 4px solid #0066FF;
            background: #00FF66;
            color: #000;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13pt;
            font-weight: 900;
            padding: 4px 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 4px 4px 0px 0px #000;
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
            .receipt-card { border: 2px solid #000; box-shadow: none; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-action">🖨️ CETAK STRUK</button>
        <button onclick="window.close()" class="btn-action" style="background:#FF003C; color:#fff;">[X] TUTUP</button>
    </div>

    <div class="receipt-card">
        {{-- Stamp Graphics --}}
        @if ($fine->status === 'paid')
            <div class="stamp-lunas">[ LUNAS ]</div>
        @elseif($fine->status === 'waived')
            <div class="stamp-waived">[ BEBAS ]</div>
        @endif

        <div class="text-center font-bold" style="font-size: 12px;">
            PERPUSTAKAAN<br>
            {{ strtoupper(setting('school_name', 'SMP NEGERI 1 PUSTAKA')) }}
        </div>
        <div class="text-center" style="font-size: 9px; margin-top: 2px;">
            {{ setting('school_address', 'Jl. Pendidikan No. 45') }}
        </div>
        
        <div class="ascii-line">================================</div>

        <div class="text-center font-bold" style="font-size: 11px;">
            @if ($fine->status === 'paid')
                [ BUKTI PELUNASAN DENDA ]
            @elseif($fine->status === 'waived')
                [ SURAT BEBAS DENDA ]
            @else
                [ STRUK TUNGGAKAN DENDA ]
            @endif
        </div>
        
        <div class="ascii-line">--------------------------------</div>

        <table style="width: 100%; font-size: 10px; border-collapse: collapse; font-family: 'JetBrains Mono', monospace;">
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

        <div class="ascii-line">--------------------------------</div>

        <div style="font-size: 10px;">
            <div class="font-bold">BUKU TERLAMBAT:</div>
            <div>{{ $fine->loanItem->bookItem->book->title ?? 'Peminjaman Buku Terlambat' }}</div>
            <div style="font-size: 9px; opacity: 0.8;">[BARCODE: {{ $fine->loanItem->bookItem->item_code ?? '-' }}]</div>
        </div>

        <div class="ascii-line">================================</div>

        <table style="width: 100%; font-size: 11px;" class="font-bold">
            <tr>
                <td>TOTAL DENDA</td>
                <td class="text-right">Rp {{ number_format($fine->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>STATUS</td>
                <td class="text-right" style="text-transform: uppercase;">[{{ $fine->status }}]</td>
            </tr>
        </table>

        @if ($fine->notes)
            <div style="font-size: 9px; margin-top: 5px;">
                CATATAN: {{ $fine->notes }}
            </div>
        @endif

        <div class="ascii-line">================================</div>

        {{-- Barcode Pembayaran jika unpaid --}}
        @if ($fine->status === 'unpaid')
            <div class="barcode-box">
                {!! DNS1D::getBarcodeSVG('FIN-'.$fine->id, 'C128', 1.0, 24, 'black') !!}
                <div style="font-size: 8px; margin-top: 3px; font-weight: bold;">SERAHKAN BARCODE INI KE KASIR</div>
            </div>
        @endif

        <div class="text-center" style="font-size: 9px; margin-top: 10px;">
            ================================<br>
            SIMPAN STRUK SEBAGAI BUKTI RESMI<br>
            *** TERIMA KASIH ***
        </div>
    </div>
</body>
</html>
html>

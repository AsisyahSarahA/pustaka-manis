<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Slip Pinjam — {{ $loan->loan_code }}</title>
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #111;
            width: 58mm;
            margin: 0 auto;
            padding: 4mm;
        }
        .center { text-align: center; }
        .bolder { font-weight: 700; }
        .line { border-top: 1px dashed #111; margin: 6px 0; }
        .row { display: flex; justify-content: space-between; }
        .mt { margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 1px 0; vertical-align: top; }
        .book-title { font-size: 11px; }
        .book-code { font-size: 10px; color: #333; }
        .no-print { font-family: Arial, sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { width: auto; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="width:100%; text-align:center; margin-bottom:10mm;">
        <button onclick="window.print()" style="padding:10px 24px;border:0;border-radius:8px;background:#3F7D75;color:#fff;font-weight:700;cursor:pointer;">Cetak Slip</button>
        <a href="{{ route('loans.index') }}" style="display:inline-block;margin-left:8px;padding:10px 24px;border-radius:8px;background:#eee;color:#333;font-weight:700;text-decoration:none;">&larr; Daftar</a>
    </div>

    <div class="center">
        <div class="bolder" style="font-weight:700">{{ setting('school_name', 'PERPUSTAKAAN SMP') }}</div>
        <div style="font-size:10px;">PERPUSTAKAAN DIGITAL</div>
        <div style="font-size:10px;">{{ setting('school_address', '') }}</div>
    </div>
    <div class="line"></div>
    <div class="center bolder" style="font-size:13px;">SLIP PEMINJAMAN</div>
    <div class="line"></div>

    <table>
        <tr><td class="bolder" style="min-width:34mm;">No. Transaksi</td><td>: {{ $loan->loan_code }}</td></tr>
        <tr><td>Tanggal</td><td>: {{ $loan->borrow_date->format('d/m/Y') }}</td></tr>
        <tr><td>Peminjam</td><td>: {{ $loan->member->name }}</td></tr>
        <tr><td>NIS/NIP</td><td>: {{ $loan->member->identity_number ?? '-' }}</td></tr>
        <tr><td>Kelas</td><td>: {{ $loan->member->department_class ?? '-' }}</td></tr>
    </table>

    <div class="line"></div>
    <div class="bolder center">DAFTAR BUKU</div>
    <div class="mt"></div>

    <table>
        @foreach ($loan->items as $idx => $item)
            <tr>
                <td style="width:6mm;">{{ $idx + 1 }}.</td>
                <td>
                    <div class="book-title bolder">{{ $item->bookItem?->book?->title }}</div>
                    <div class="book-code">{{ $item->bookItem?->item_code }}</div>
                </td>
            </tr>
        @endforeach
    </table>

    <div class="line"></div>
    <div class="row bolder">
        <span>Jatuh Tempo</span>
        <span>{{ $loan->due_date->format('d/m/Y') }}</span>
    </div>
    <div class="row bolder">
        <span>Total Buku</span>
        <span>{{ $loan->items_count ?? $loan->items->count() }}</span>
    </div>
    <div class="line"></div>

    <div class="center mt">
        <div style="font-size:11px;">Kembalikan tepat waktu ya!</div>
        <div class="mt" style="font-size:9px;">Dibuat oleh: {{ $loan->user?->name }}</div>
        <div style="font-size:9px;" class="mt">{{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <script>
        window.onload = function () {
            setTimeout(() => window.print(), 300);
        };
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <style>
        @page {
            margin: 8mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            background: #fff;
        }
        .print-area { display: block; }
        .no-print { display: block; text-align: center; margin-bottom: 12px; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding:10px 24px;border:0;border-radius:999px;background:#3F7D75;color:#fff;font-weight:600;cursor:pointer;">🖨️ Cetak Kartu</button>
        <a href="#" onclick="history.back(); return false;" style="display:inline-block;margin-left:8px;padding:10px 20px;border-radius:999px;background:#e2e8f0;color:#334155;font-weight:600;text-decoration:none;">&larr; Kembali</a>
    </div>
    <div class="print-area">
        @yield('content')
    </div>
</body>
</html>

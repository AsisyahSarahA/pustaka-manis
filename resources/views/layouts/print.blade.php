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
        <button onclick="window.print()" style="padding:10px 24px;border:0;border-radius:999px;background:#3F7D75;color:#fff;font-weight:600;cursor:pointer;">Cetak Kartu</button>
    </div>
    <div class="print-area">
        @yield('content')
    </div>
</body>
</html>

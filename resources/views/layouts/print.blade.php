<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&display=swap');
        @page {
            margin: 6mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'JetBrains Mono', 'Courier New', Courier, monospace;
            margin: 0;
            background: #F4F4F0;
            color: #000000;
        }
        .print-area { display: block; }
        .no-print { display: flex; justify-content: center; gap: 12px; margin-bottom: 16px; padding-top: 16px; }
        .btn-print {
            padding: 10px 20px;
            border: 3px solid #000;
            background: #00FF66;
            color: #000;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 4px 4px 0px 0px #000;
        }
        .btn-print:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0px 0px #000; }
        .btn-back {
            padding: 10px 20px;
            border: 3px solid #000;
            background: #FFDE00;
            color: #000;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: none;
            box-shadow: 4px 4px 0px 0px #000;
        }
        .btn-back:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0px 0px #000; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ CETAK [PRINT]</button>
        <a href="#" onclick="history.back(); return false;" class="btn-back">◄ KEMBALI</a>
    </div>
    <div class="print-area">
        @yield('content')
    </div>
</body>
</html>


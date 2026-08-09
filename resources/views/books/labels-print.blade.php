<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Label Barcode Buku</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 8mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .no-print {
            background-color: #0f172a;
            color: #ffffff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .btn-print {
            background-color: #38bdf8;
            color: #0f172a;
            border: none;
            padding: 8px 18px;
            font-weight: bold;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
        }
        .label-grid {
            display: grid;
            grid-template-columns: repeat(3, 50mm);
            gap: 4mm 6mm;
            justify-content: center;
            padding: 10px;
        }
        /* Layout per label: 5cm x 3cm */
        .label-card {
            width: 50mm;
            height: 30mm;
            box-sizing: border-box;
            border: 1px dashed #94a3b8;
            border-radius: 4px;
            padding: 2mm 3mm;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .school-header {
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .book-title {
            font-size: 7.5pt;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.1;
            margin: 1.5mm 0 1mm 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 5.5mm;
        }
        .barcode-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 10mm;
            margin: 0 auto;
        }
        .barcode-container svg, .barcode-container img {
            max-width: 44mm;
            max-height: 9.5mm;
        }
        .item-code-text {
            font-family: 'Courier New', Courier, monospace;
            font-size: 7.5pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin-top: 1px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
            }
            .label-card {
                border: 0.5px solid #cbd5e1;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div>
            <strong>Preview Cetak Label Barcode (Ukuran 5 x 3 cm)</strong>
            <div style="font-size: 11px; opacity: 0.8;">Total: {{ count($items) }} eksemplar label</div>
        </div>
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Sekarang</button>
    </div>

    <div class="label-grid">
        @foreach ($items as $item)
            <div class="label-card">
                <div class="school-header">
                    PERPUS {{ strtoupper(setting('school_name', 'SMP N 1 PUSTAKA')) }}
                </div>
                <div class="book-title">
                    {{ $item->book->title ?? 'Buku' }}
                </div>
                <div class="barcode-container">
                    {!! DNS1D::getBarcodeSVG($item->item_code, 'C128', 1.0, 26, '#0f172a', false) !!}
                </div>
                <div class="item-code-text">
                    {{ $item->item_code }}
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>

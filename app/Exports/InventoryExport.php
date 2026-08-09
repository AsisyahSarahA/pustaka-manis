<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InventoryExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = collect();
        $books = $this->data['books'] ?? [];
        $totalItemsAcc = 0;
        $availableItemsAcc = 0;

        foreach ($books as $index => $book) {
            $totalCount = $book->items_count ?? $book->items->count();
            $availCount = $book->items->where('status', 'tersedia')->count();
            
            $totalItemsAcc += $totalCount;
            $availableItemsAcc += $availCount;

            $rows->push([
                'no' => $index + 1,
                'book_code' => $book->book_code,
                'title' => $book->title,
                'category' => $book->category->name ?? '-',
                'author' => $book->author ?? '-',
                'publisher' => $book->publisher ?? '-',
                'isbn' => $book->isbn ?? '-',
                'total_items' => $totalCount,
                'available_items' => $availCount,
            ]);
        }

        // Add Summary/Total Row at the bottom
        $rows->push([
            'no' => '',
            'book_code' => '',
            'title' => '',
            'category' => '',
            'author' => '',
            'publisher' => '',
            'isbn' => 'TOTAL AKUMULASI:',
            'total_items' => $totalItemsAcc,
            'available_items' => $availableItemsAcc,
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN INVENTARIS KOLEKSI BUKU PERPUSTAKAAN'],
            ['Dicetak pada: ' . now()->translatedFormat('d F Y H:i')],
            [''],
            ['No', 'Kode Buku', 'Judul Buku', 'Kategori', 'Penulis', 'Penerbit', 'ISBN', 'Total Eksemplar', 'Eksemplar Tersedia']
        ];
    }

    public function title(): string
    {
        return 'Inventaris Buku';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->data['books'] ?? []) + 5;

        // Title row styles
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(11);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Row 4 Soft Navy style
        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'], // Soft Navy
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Borders & Alignments
        $sheet->getStyle('A4:I' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);

        // Total Row Style
        $sheet->getStyle('A' . $lastRow . ':I' . $lastRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '0F172A'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0'],
            ],
        ]);

        return [];
    }
}

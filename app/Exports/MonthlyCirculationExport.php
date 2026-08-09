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

class MonthlyCirculationExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = collect();
        $loans = $this->data['loans'] ?? [];

        foreach ($loans as $index => $loan) {
            $rows->push([
                'no' => $index + 1,
                'loan_code' => $loan->loan_code,
                'member_name' => $loan->member->name ?? '-',
                'member_type' => ucfirst($loan->member->type ?? 'siswa') . ($loan->member->department_class ? ' ('.$loan->member->department_class.')' : ''),
                'borrow_date' => \Carbon\Carbon::parse($loan->borrow_date)->format('d/m/Y'),
                'due_date' => \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y'),
                'status' => ucfirst($loan->status),
                'book_count' => $loan->items->count(),
            ]);
        }

        // Add Summary/Total Row at the bottom
        $rows->push([
            'no' => '',
            'loan_code' => '',
            'member_name' => '',
            'member_type' => '',
            'borrow_date' => '',
            'due_date' => '',
            'status' => 'TOTAL TRANSAKSI:',
            'book_count' => count($loans),
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN SIRKULASI BULANAN PERPUSTAKAAN'],
            ['Periode: ' . ($this->data['month_name'] ?? '')],
            [''],
            ['No', 'Kode Pinjam', 'Nama Anggota', 'Kategori / Kelas', 'Tgl Pinjam', 'Jatuh Tempo', 'Status', 'Jumlah Buku']
        ];
    }

    public function title(): string
    {
        return 'Laporan Sirkulasi';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->data['loans'] ?? []) + 5;

        // Title row styles
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(11);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Row 4 Soft Navy style
        $sheet->getStyle('A4:H4')->applyFromArray([
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

        // Borders & Alignments for Data Rows
        $sheet->getStyle('A4:H' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);

        // Total Row Style
        $sheet->getStyle('A' . $lastRow . ':H' . $lastRow)->applyFromArray([
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

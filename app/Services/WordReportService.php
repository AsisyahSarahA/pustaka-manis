<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TextAlignment;

class WordReportService
{
    /**
     * Generate editable Word DOCX for "Surat Keterangan Kehilangan Buku".
     */
    public function generateLostBookCertificate(array $data): string
    {
        $phpWord = new PhpWord();
        
        // Define styles
        $phpWord->addTitleStyle(1, ['name' => 'Calibri', 'size' => 14, 'bold' => true, 'color' => '0F172A'], ['alignment' => Jc::CENTER]);
        
        $section = $phpWord->addSection([
            'marginTop' => 1440,
            'marginRight' => 1440,
            'marginBottom' => 1440,
            'marginLeft' => 1440,
        ]);

        // Kop Surat Header
        $headerTable = $section->addTable(['width' => 100 * 50, 'unit' => 'pct']);
        $headerTable->addRow();
        $cell = $headerTable->addCell(100 * 50);
        $cell->addText(strtoupper(setting('school_name', 'SMP NEGERI 1 PUSTAKA')), ['name' => 'Calibri', 'size' => 14, 'bold' => true], ['alignment' => Jc::CENTER]);
        $cell->addText('PERPUSTAKAAN "PUSTAKAMANIS"', ['name' => 'Calibri', 'size' => 12, 'bold' => true, 'color' => '0F172A'], ['alignment' => Jc::CENTER]);
        $cell->addText(setting('school_address', 'Jl. Pendidikan No. 45 Telp. (021) 555-0199'), ['name' => 'Calibri', 'size' => 9, 'italic' => true], ['alignment' => Jc::CENTER]);
        
        $section->addTextBreak(1);
        $section->addText('SURAT KETERANGAN KEHILANGAN BUKU', ['name' => 'Calibri', 'size' => 13, 'bold' => true, 'underline' => 'single'], ['alignment' => Jc::CENTER]);
        $section->addText('Nomor: ' . ($data['doc_number'] ?? '045/PERPUS/SKK/' . date('Y')), ['name' => 'Calibri', 'size' => 10], ['alignment' => Jc::CENTER]);

        $section->addTextBreak(1);
        $section->addText('Yang bertanda tangan di bawah ini, Kepala Perpustakaan ' . setting('school_name', 'SMP Negeri 1 Pustaka') . ', menerangkan bahwa:', ['name' => 'Calibri', 'size' => 11]);

        $section->addTextBreak(1);
        
        // Member Info Table
        $table = $section->addTable(['borderSize' => 0]);
        
        $table->addRow();
        $table->addCell(2500)->addText('Nama Anggota', ['name' => 'Calibri', 'size' => 10, 'bold' => true]);
        $table->addCell(500)->addText(':', ['name' => 'Calibri', 'size' => 10]);
        $table->addCell(5000)->addText($data['member_name'] ?? '-', ['name' => 'Calibri', 'size' => 10, 'bold' => true]);

        $table->addRow();
        $table->addCell(2500)->addText('NISN / NIP / NIK', ['name' => 'Calibri', 'size' => 10]);
        $table->addCell(500)->addText(':', ['name' => 'Calibri', 'size' => 10]);
        $table->addCell(5000)->addText($data['member_number'] ?? '-', ['name' => 'Calibri', 'size' => 10]);

        $table->addRow();
        $table->addCell(2500)->addText('Kategori / Kelas', ['name' => 'Calibri', 'size' => 10]);
        $table->addCell(500)->addText(':', ['name' => 'Calibri', 'size' => 10]);
        $table->addCell(5000)->addText($data['member_category'] ?? '-', ['name' => 'Calibri', 'size' => 10]);

        $section->addTextBreak(1);
        $section->addText('Telah melaporkan kehilangan barang inventaris koleksi perpustakaan berupa:', ['name' => 'Calibri', 'size' => 11]);
        $section->addTextBreak(1);

        // Book Info Table
        $bookTable = $section->addTable(['borderSize' => 6, 'borderColor' => '0F172A', 'cellMargin' => 80]);
        
        $bookTable->addRow();
        $bookTable->addCell(2000)->addText('Kode Eksemplar', ['name' => 'Calibri', 'size' => 10, 'bold' => true]);
        $bookTable->addCell(4000)->addText('Judul Buku', ['name' => 'Calibri', 'size' => 10, 'bold' => true]);
        $bookTable->addCell(2000)->addText('Penulis', ['name' => 'Calibri', 'size' => 10, 'bold' => true]);

        $bookTable->addRow();
        $bookTable->addCell(2000)->addText($data['item_code'] ?? '-', ['name' => 'Calibri', 'size' => 10]);
        $bookTable->addCell(4000)->addText($data['book_title'] ?? '-', ['name' => 'Calibri', 'size' => 10, 'bold' => true]);
        $bookTable->addCell(2000)->addText($data['book_author'] ?? '-', ['name' => 'Calibri', 'size' => 10]);

        $section->addTextBreak(1);
        $section->addText('Demikian Surat Keterangan ini dibuat agar dapat dipergunakan sebagaimana mestinya untuk proses penggantian koleksi atau penyelesaian administrasi denda.', ['name' => 'Calibri', 'size' => 11]);

        $section->addTextBreak(2);

        // Signature block
        $sigTable = $section->addTable(['width' => 100 * 50, 'unit' => 'pct']);
        $sigTable->addRow();
        $leftCell = $sigTable->addCell(4000);
        $leftCell->addText('Pemohon / Yang Melaporkan,', ['name' => 'Calibri', 'size' => 10]);
        $leftCell->addTextBreak(3);
        $leftCell->addText('(' . ($data['member_name'] ?? '...........................') . ')', ['name' => 'Calibri', 'size' => 10, 'bold' => true]);

        $rightCell = $sigTable->addCell(4000);
        $rightCell->addText(setting('school_city', 'Kota Pustaka') . ', ' . now()->translatedFormat('d F Y'), ['name' => 'Calibri', 'size' => 10]);
        $rightCell->addText('Kepala Perpustakaan,', ['name' => 'Calibri', 'size' => 10]);
        $rightCell->addTextBreak(3);
        $rightCell->addText(setting('librarian_name', 'Nurhayati, S.IP'), ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'underline' => 'single']);
        $rightCell->addText('NIP. ' . setting('librarian_nip', '19820914 200801 2 011'), ['name' => 'Calibri', 'size' => 9]);

        $tempFile = tempnam(sys_get_temp_dir(), 'skk_') . '.docx';
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return $tempFile;
    }

    /**
     * Generate editable Word DOCX for "Laporan Sirkulasi Bulanan (Dinas)".
     */
    public function generateMonthlyReportDoc(array $data): string
    {
        $phpWord = new PhpWord();
        
        $section = $phpWord->addSection([
            'marginTop' => 1440,
            'marginRight' => 1440,
            'marginBottom' => 1440,
            'marginLeft' => 1440,
        ]);

        // Kop Surat Header
        $section->addText(strtoupper(setting('school_name', 'SMP NEGERI 1 PUSTAKA')), ['name' => 'Calibri', 'size' => 14, 'bold' => true], ['alignment' => Jc::CENTER]);
        $section->addText('LAPORAN EKSEKUTIF SIRKULASI PERPUSTAKAAN', ['name' => 'Calibri', 'size' => 12, 'bold' => true, 'color' => '0F172A'], ['alignment' => Jc::CENTER]);
        $section->addText('Periode: ' . ($data['month_name'] ?? date('F Y')), ['name' => 'Calibri', 'size' => 10, 'italic' => true], ['alignment' => Jc::CENTER]);
        
        $section->addTextBreak(1);

        // Summary Table
        $summaryTable = $section->addTable(['borderSize' => 6, 'borderColor' => '0F172A', 'cellMargin' => 100]);
        $summaryTable->addRow();
        $summaryTable->addCell(3000)->addText('Total Peminjaman Buku', ['name' => 'Calibri', 'size' => 10, 'bold' => true]);
        $summaryTable->addCell(5000)->addText(($data['total_borrowed_items'] ?? 0) . ' Eksemplar', ['name' => 'Calibri', 'size' => 10]);

        $summaryTable->addRow();
        $summaryTable->addCell(3000)->addText('Total Pengembalian Buku', ['name' => 'Calibri', 'size' => 10, 'bold' => true]);
        $summaryTable->addCell(5000)->addText(($data['returns_count'] ?? 0) . ' Eksemplar', ['name' => 'Calibri', 'size' => 10]);

        $summaryTable->addRow();
        $summaryTable->addCell(3000)->addText('Jumlah Pengunjung', ['name' => 'Calibri', 'size' => 10, 'bold' => true]);
        $summaryTable->addCell(5000)->addText(($data['visitors_count'] ?? 0) . ' Orang', ['name' => 'Calibri', 'size' => 10]);

        $summaryTable->addRow();
        $summaryTable->addCell(3000)->addText('Denda Terkumpul', ['name' => 'Calibri', 'size' => 10, 'bold' => true]);
        $summaryTable->addCell(5000)->addText('Rp ' . number_format($data['total_fine_amount'] ?? 0, 0, ',', '.'), ['name' => 'Calibri', 'size' => 10]);

        $section->addTextBreak(2);
        
        // Signature Block
        $sigTable = $section->addTable(['width' => 100 * 50, 'unit' => 'pct']);
        $sigTable->addRow();
        $leftCell = $sigTable->addCell(4000);
        $leftCell->addText('Mengetahui,<br>Kepala Sekolah', ['name' => 'Calibri', 'size' => 10]);
        $leftCell->addTextBreak(3);
        $leftCell->addText(setting('headmaster_name', 'Drs. H. Ahmad Dahlan, M.Pd'), ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'underline' => 'single']);
        $leftCell->addText('NIP. ' . setting('headmaster_nip', '19680512 199403 1 004'), ['name' => 'Calibri', 'size' => 9]);

        $rightCell = $sigTable->addCell(4000);
        $rightCell->addText(setting('school_city', 'Kota Pustaka') . ', ' . now()->translatedFormat('d F Y'), ['name' => 'Calibri', 'size' => 10]);
        $rightCell->addText('Pustakawan Utama,', ['name' => 'Calibri', 'size' => 10]);
        $rightCell->addTextBreak(3);
        $rightCell->addText(setting('librarian_name', 'Nurhayati, S.IP'), ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'underline' => 'single']);
        $rightCell->addText('NIP. ' . setting('librarian_nip', '19820914 200801 2 011'), ['name' => 'Calibri', 'size' => 9]);

        $tempFile = tempnam(sys_get_temp_dir(), 'report_doc_') . '.docx';
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return $tempFile;
    }
}

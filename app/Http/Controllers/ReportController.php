<?php

namespace App\Http\Controllers;

use App\Exports\InventoryExport;
use App\Exports\MonthlyCirculationExport;
use App\Models\Category;
use App\Models\LoanItem;
use App\Services\ReportService;
use App\Services\WordReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;

class ReportController extends Controller
{
    protected ReportService $reportService;
    protected WordReportService $wordReportService;

    public function __construct(ReportService $reportService, WordReportService $wordReportService)
    {
        $this->reportService = $reportService;
        $this->wordReportService = $wordReportService;
    }

    public function index(Request $request): View
    {
        $type = $request->query('type', 'monthly');

        $data = $this->buildReportData($type, $request);
        $data['type'] = $type;

        // Default variabel filter agar form tidak error saat query kosong / salah tipe.
        $data += [
            'start' => Carbon::now()->startOfMonth()->toDateString(),
            'end' => Carbon::now()->toDateString(),
            'class' => '',
            'visitor_type' => '',
            'month' => (int) Carbon::now()->format('m'),
            'year' => (int) Carbon::now()->format('Y'),
            'category_id' => null,
            'status' => '',
            'categories' => Category::orderBy('name')->get(),
        ];

        return view('reports.index', $data);
    }

    public function export(Request $request)
    {
        $type = $request->query('type', 'monthly');
        $format = strtolower($request->query('format', 'pdf'));

        $data = $this->buildReportData($type, $request);
        $data['type'] = $type;

        if ($format === 'excel') {
            if ($type === 'inventory') {
                return Excel::download(new InventoryExport($data), 'laporan-inventaris-' . now()->format('Y-m-d') . '.xlsx');
            }

            if ($type === 'monthly') {
                $filename = 'laporan-sirkulasi-' . $data['year'] . '-' . sprintf('%02d', $data['month']) . '.xlsx';
                return Excel::download(new MonthlyCirculationExport($data), $filename);
            }

            // Loans / overdue / visitors: belum ada export Excel khusus → lanjut ke PDF.
        }

        if ($format === 'word' && $type === 'monthly') {
            $tempFile = $this->wordReportService->generateMonthlyReportDoc($data);
            $filename = 'laporan-sirkulasi-' . $data['year'] . '-' . sprintf('%02d', $data['month']) . '.docx';
            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        }

        $pdf = $this->reportService->generatePdf($type, $data);

        $base = match ($type) {
            'inventory' => 'laporan-inventaris',
            'visitors' => 'laporan-kunjungan',
            'overdue' => 'laporan-keterlambatan',
            'loans' => 'laporan-peminjaman',
            default => 'laporan-sirkulasi',
        };

        return $pdf->download($base . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Susun data laporan sesuai tipe. Menghindari N+1 query.
     */
    protected function buildReportData(string $type, Request $request): array
    {
        return match ($type) {
            'inventory' => $this->reportService->getInventoryData(
                $request->query('category_id'),
                $request->query('status')
            ),
            'visitors' => $this->reportService->getVisitorReportData($request),
            'overdue' => $this->reportService->getLoanReportData($request, overdueOnly: true),
            'monthly' => $this->normalizeMonthly($this->reportService->getMonthlyCirculationData(
                (int) $request->query('month', Carbon::now()->format('m')),
                (int) $request->query('year', Carbon::now()->format('Y'))
            )),
            default => $this->reportService->getLoanReportData($request),
        };
    }

    /**
     * Samakan nama variabel laporan bulanan agar sesuai ekspektasi view.
     */
    protected function normalizeMonthly(array $data): array
    {
        return [
            ...$data,
            'returns' => $data['returns_count'],
            'visitors' => $data['visitors_count'],
            'totalBorrowed' => $data['total_borrowed_items'],
            'totalFine' => $data['total_fine_amount'],
            'overdue' => $data['overdue_count'],
        ];
    }

    /**
     * Export Word DOCX Surat Keterangan Kehilangan Buku.
     */
    public function exportLostBookWord(Request $request, LoanItem $loanItem)
    {
        $loanItem->load(['loan.member', 'bookItem.book']);

        $data = [
            'doc_number' => '045/PERPUS/SKK/' . date('Y') . '/' . sprintf('%04d', $loanItem->id),
            'member_name' => $loanItem->loan->member->name ?? 'N/A',
            'member_number' => $loanItem->loan->member->member_code ?? ($loanItem->loan->member->identity_number ?? '-'),
            'member_category' => ucfirst($loanItem->loan->member->type ?? 'Siswa') . ($loanItem->loan->member->department_class ? ' ('.$loanItem->loan->member->department_class.')' : ''),
            'item_code' => $loanItem->bookItem->item_code ?? '-',
            'book_title' => $loanItem->bookItem->book->title ?? '-',
            'book_author' => $loanItem->bookItem->book->author ?? '-',
        ];

        $tempFile = $this->wordReportService->generateLostBookCertificate($data);
        $filename = 'surat-kehilangan-' . str_replace(' ', '-', strtolower($data['member_name'])) . '.docx';

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}

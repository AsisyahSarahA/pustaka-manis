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
        $period = $this->reportService->resolvePeriod($request);

        $data = $this->buildReportData($type, $request, $period);
        $data['type'] = $type;

        // Variabel filter untuk form
        $data += [
            'period_type' => $period['period_type'],
            'year' => $period['year'],
            'month' => $period['month'],
            'week' => $period['week'],
            'start' => $period['start'],
            'end' => $period['end'],
            'period_label' => $period['period_label'],
            'class' => '',
            'visitor_type' => '',
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
        $period = $this->reportService->resolvePeriod($request);

        $data = $this->buildReportData($type, $request, $period);
        $data['type'] = $type;

        $slugPeriod = str_replace([' ', '/', '\\'], '-', strtolower($period['period_label']));

        if ($format === 'excel') {
            if ($type === 'inventory') {
                return Excel::download(new InventoryExport($data), 'laporan-inventaris-' . now()->format('Y-m-d') . '.xlsx');
            }

            $filename = 'laporan-sirkulasi-' . $slugPeriod . '.xlsx';
            return Excel::download(new MonthlyCirculationExport($data), $filename);
        }

        if ($format === 'word' && ($type === 'monthly' || $type === 'circulation')) {
            $tempFile = $this->wordReportService->generateMonthlyReportDoc($data);
            $filename = 'laporan-sirkulasi-' . $slugPeriod . '.docx';
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

        return $pdf->download($base . '-' . $slugPeriod . '.pdf');
    }

    /**
     * Susun data laporan sesuai tipe & periode yang dipilih.
     */
    protected function buildReportData(string $type, Request $request, array $period): array
    {
        return match ($type) {
            'inventory' => $this->reportService->getInventoryData(
                $request->query('category_id'),
                $request->query('status')
            ),
            'visitors' => $this->reportService->getVisitorReportData($request, $period),
            'overdue' => $this->reportService->getLoanReportData($request, $period, overdueOnly: true),
            'monthly', 'circulation' => $this->normalizeMonthly($this->reportService->getCirculationData($period)),
            default => $this->reportService->getLoanReportData($request, $period),
        };
    }

    /**
     * Samakan nama variabel laporan sirkulasi agar sesuai ekspektasi view.
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

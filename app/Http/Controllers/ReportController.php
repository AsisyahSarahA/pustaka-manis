<?php

namespace App\Http\Controllers;

use App\Exports\InventoryExport;
use App\Exports\MonthlyCirculationExport;
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

        if ($type === 'inventory') {
            $data = $this->reportService->getInventoryData(
                $request->query('category_id'),
                $request->query('status')
            );
        } else {
            $month = (int) $request->query('month', Carbon::now()->format('m'));
            $year = (int) $request->query('year', Carbon::now()->format('Y'));
            $data = $this->reportService->getMonthlyCirculationData($month, $year);
        }

        $data['type'] = $type;

        return view('reports.index', $data);
    }

    public function export(Request $request)
    {
        $type = $request->query('type', 'monthly');
        $format = strtolower($request->query('format', 'pdf'));

        if ($type === 'inventory') {
            $data = $this->reportService->getInventoryData(
                $request->query('category_id'),
                $request->query('status')
            );

            if ($format === 'excel') {
                return Excel::download(new InventoryExport($data), 'laporan-inventaris-' . now()->format('Y-m-d') . '.xlsx');
            }

            $pdf = $this->reportService->generatePdf('inventory', $data);
            return $pdf->download('laporan-inventaris-' . now()->format('Y-m-d') . '.pdf');
        }

        // Default: Monthly Circulation
        $month = (int) $request->query('month', Carbon::now()->format('m'));
        $year = (int) $request->query('year', Carbon::now()->format('Y'));
        $data = $this->reportService->getMonthlyCirculationData($month, $year);

        if ($format === 'excel') {
            return Excel::download(new MonthlyCirculationExport($data), 'laporan-sirkulasi-' . $year . '-' . sprintf('%02d', $month) . '.xlsx');
        }

        if ($format === 'word') {
            $tempFile = $this->wordReportService->generateMonthlyReportDoc($data);
            return response()->download($tempFile, 'laporan-sirkulasi-' . $year . '-' . sprintf('%02d', $month) . '.docx')->deleteFileAfterSend(true);
        }

        // Default PDF
        $pdf = $this->reportService->generatePdf('monthly', $data);
        return $pdf->download('laporan-sirkulasi-' . $year . '-' . sprintf('%02d', $month) . '.pdf');
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

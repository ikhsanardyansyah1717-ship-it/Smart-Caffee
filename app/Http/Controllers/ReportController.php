<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Services\FinancialReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$start, $end] = FinancialReportService::resolvePeriod(
            $request->query('month'),
            $request->query('start'),
            $request->query('end')
        );
        $financialReport = app(FinancialReportService::class);
        $financialSummary = $financialReport->summary($start, $end);
        $periodLabel = FinancialReportService::periodLabel($start, $end);

        $totalPendapatan = $financialSummary['pendapatan'];
        $topProducts = $financialReport->topProducts($start, $end);

        return view('owner.reports', compact(
            'totalPendapatan',
            'topProducts',
            'periodLabel',
            'financialSummary',
            'start',
            'end'
        ));
    }

    public function exportExcel(Request $request)
    {
        [$start, $end] = FinancialReportService::resolvePeriod(
            $request->query('month'),
            $request->query('start'),
            $request->query('end')
        );

        return Excel::download(
            new ReportExport($start, $end),
            'laporan-bisnis-' . $start->format('Ymd') . '-' . $end->format('Ymd') . '.xlsx'
        );
    }

    public function exportPdf(Request $request): Response
    {
        [$start, $end] = FinancialReportService::resolvePeriod(
            $request->query('month'),
            $request->query('start'),
            $request->query('end')
        );
        $financialReport = app(FinancialReportService::class);
        $financialSummary = $financialReport->summary($start, $end);
        $topProducts = $financialReport->topProducts($start, $end);
        $periodLabel = FinancialReportService::periodLabel($start, $end);

        $pdf = Pdf::loadView('owner.reports-pdf', compact('financialSummary', 'topProducts', 'periodLabel'));

        return $pdf->download('laporan-bisnis-' . $start->format('Ymd') . '-' . $end->format('Ymd') . '.pdf');
    }
}

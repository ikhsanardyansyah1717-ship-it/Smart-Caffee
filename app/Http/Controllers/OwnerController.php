<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\FinancialReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class OwnerController extends Controller
{
    /**
     * ============================================================
     * DASHBOARD OWNER
     * ============================================================
     */
    public function dashboard(Request $request): View
    {
        [$periodStart, $periodEnd] = FinancialReportService::resolvePeriod(
            $request->query('month'),
            $request->query('start'),
            $request->query('end')
        );
        $periodLabel = FinancialReportService::periodLabel($periodStart, $periodEnd);
        $financialReport = app(FinancialReportService::class);
        $financialSummary = $financialReport->summary($periodStart, $periodEnd);

        $penjualanHariIni = DB::table('payments')
            ->where('status', 'Berhasil')
            ->whereBetween('paid_at', [$periodStart, $periodEnd])
            ->sum('amount');

        $transaksiHariIni = DB::table('payments')
            ->where('status', 'Berhasil')
            ->whereBetween('paid_at', [$periodStart, $periodEnd])
            ->count();

        $pelangganBaruHariIni = User::where('role', 'customer')
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->count();

        $ordersInPeriod = Order::whereBetween('created_at', [$periodStart, $periodEnd]);
        $pesananMenunggu = (clone $ordersInPeriod)->where('payment_status', 'Dibayar')
            ->where('status', 'Menunggu')->count();
        $pesananDiproses = (clone $ordersInPeriod)->where('payment_status', 'Dibayar')
            ->where('status', 'Diproses')->count();
        $pesananSelesai = (clone $ordersInPeriod)->where('payment_status', 'Dibayar')
            ->where('status', 'Selesai')->count();

        $menuAktif = Product::where('is_available', true)->count();
        $weeklyTrend = $this->dailySalesTrend($periodStart, $periodEnd);

        $transaksiTerbaru = Order::with(['payment', 'user'])
            ->whereHas('payment', function ($query) use ($periodStart, $periodEnd) {
                $query->where('status', 'Berhasil')
                    ->whereBetween('paid_at', [$periodStart, $periodEnd]);
            })
            ->latest()
            ->limit(5)
            ->get();

        $pelangganTeratas = $financialReport->topCustomers($periodStart, $periodEnd);

        return view('owner.dashboard', compact(
            'penjualanHariIni', 'transaksiHariIni', 'pelangganBaruHariIni',
            'pesananMenunggu', 'pesananDiproses', 'pesananSelesai', 'menuAktif',
            'weeklyTrend', 'transaksiTerbaru', 'financialSummary', 'periodLabel',
            'periodStart', 'periodEnd', 'pelangganTeratas'
        ));
    }


    /**
     * ============================================================
     * PENJUALAN
     * ============================================================
     */
    public function sales(Request $request): View
    {
        [$periodStart, $periodEnd] = FinancialReportService::resolvePeriod($request->query('month'), $request->query('start'), $request->query('end'));
        $selectedMonth = $periodStart->format('Y-m');
        $periodLabel = FinancialReportService::periodLabel($periodStart, $periodEnd);
        $financialSummary = app(FinancialReportService::class)->summary($periodStart, $periodEnd);

        $omzetBulanIni = DB::table('payments')
            ->where('status', 'Berhasil')
            ->whereBetween('paid_at', [$periodStart, $periodEnd])
            ->sum('amount');
        $transaksiBulanIni = DB::table('payments')
            ->where('status', 'Berhasil')
            ->whereBetween('paid_at', [$periodStart, $periodEnd])
            ->count();
        $rataRataOrder = $transaksiBulanIni > 0
            ? $omzetBulanIni / $transaksiBulanIni
            : 0;
        $dailyTrend = $this->dailySalesTrend($periodStart, $periodEnd);
        $metodePembayaran = DB::table('payments')
            ->where('status', 'Berhasil')
            ->whereIn('payment_method', ['QRIS', 'Cash', 'Bank Transfer'])
            ->whereBetween('paid_at', [$periodStart, $periodEnd])
            ->selectRaw('payment_method, COUNT(*) as jumlah')
            ->groupBy('payment_method')
            ->pluck('jumlah', 'payment_method');
        $totalMetode = $metodePembayaran->sum();
        $riwayatPenjualan = Order::with(['payment', 'user'])
            ->whereHas('payment', function ($query) use ($periodStart, $periodEnd) {
                $query->where('status', 'Berhasil')->whereBetween('paid_at', [$periodStart, $periodEnd]);
            })
            ->latest()
            ->limit(20)
            ->get();


        return view('owner.sales', compact(
            'omzetBulanIni',
            'transaksiBulanIni',
            'rataRataOrder',
            'dailyTrend',
            'metodePembayaran',
            'totalMetode',
            'riwayatPenjualan',
            'selectedMonth',
            'periodStart',
            'periodEnd',
            'financialSummary',
            'periodLabel'
        ));
    }


    /**
     * ============================================================
     * EXPORT EXCEL
     * ============================================================
     */
    public function exportSalesExcel(Request $request)
    {
        [$startOfMonth, $endOfMonth] = FinancialReportService::resolvePeriod($request->query('month'), $request->query('start'), $request->query('end'));
        $filenamePeriod = $startOfMonth->format('Ymd') . '-' . $endOfMonth->format('Ymd');

        return Excel::download(
            new SalesExport($startOfMonth, $endOfMonth),
            'laporan-penjualan-' . $filenamePeriod . '.xlsx'
        );
    }


    /**
     * ============================================================
     * EXPORT PDF
     * ============================================================
     */
    public function exportSalesPdf(Request $request): Response
    {
        [$startOfMonth, $endOfMonth] = FinancialReportService::resolvePeriod($request->query('month'), $request->query('start'), $request->query('end'));
        $filenamePeriod = $startOfMonth->format('Ymd') . '-' . $endOfMonth->format('Ymd');
        $omzetBulanIni = DB::table('payments')
            ->where('status', 'Berhasil')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $orders = Order::with('payment')
            ->whereHas('payment', function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('status', 'Berhasil')->whereBetween('paid_at', [$startOfMonth, $endOfMonth]);
            })
            ->latest()
            ->get();


        $transaksiBulanIni = $orders->count();


        $periodLabel = FinancialReportService::periodLabel($startOfMonth, $endOfMonth);


        $pdf = Pdf::loadView(
            'owner.sales-pdf',
            compact(
                'omzetBulanIni',
                'transaksiBulanIni',
                'orders',
                'periodLabel'
            )
        );


        return $pdf->download('laporan-penjualan-' . $filenamePeriod . '.pdf');
    }


    /**
     * ============================================================
     * RESOLVE PERIOD
     * ============================================================
     */
    /**
     * ============================================================
     * DAILY SALES TREND
     * ============================================================
     */
    private function dailySalesTrend(
        Carbon $start,
        Carbon $end
    ) {
        $trend = collect();

        $cursor = $start->copy()->startOfDay();
        $latestVisibleDate = $end->copy()->min(now())->endOfDay();

        // Jangan tampilkan tanggal masa depan pada periode bulan berjalan.
        if ($cursor->gt($latestVisibleDate)) {
            return $trend;
        }


        while ($cursor->lte($latestVisibleDate)) {

            $total = DB::table('payments')
                ->where('status', 'Berhasil')
                ->whereDate(
                    'paid_at',
                    $cursor->toDateString()
                )
                ->sum('amount');


            $trend->push([
                'label' => $cursor->translatedFormat('d M'),
                'date' => $cursor->toDateString(),
                'total' => (float) $total,
            ]);


            $cursor->addDay();
        }


        return $trend;
    }


}

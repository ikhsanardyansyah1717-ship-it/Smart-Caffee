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
use Illuminate\Support\Carbon;
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

        $transaksiTerbaru = Order::with('payment')
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
        [
            $startOfMonth,
            $endOfMonth,
            $selectedMonth,
            $selectedDate
        ] = $this->resolvePeriod($request);

        $financialSummary = app(FinancialReportService::class)->summary(
            $startOfMonth,
            $endOfMonth
        );
        $periodLabel = FinancialReportService::periodLabel($startOfMonth, $endOfMonth);


        /*
        |--------------------------------------------------------------------------
        | OMZET PERIODE
        |--------------------------------------------------------------------------
        | Sama dengan Dashboard:
        | payments.status = Berhasil
        | payments.paid_at = periode yang dipilih
        */
        $omzetBulanIni = DB::table('payments')
            ->where('status', 'Berhasil')
            ->whereBetween('paid_at', [
                $startOfMonth,
                $endOfMonth
            ])
            ->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | TRANSAKSI PERIODE
        |--------------------------------------------------------------------------
        */
        $transaksiBulanIni = DB::table('payments')
            ->where('status', 'Berhasil')
            ->whereBetween('paid_at', [
                $startOfMonth,
                $endOfMonth
            ])
            ->count();


        /*
        |--------------------------------------------------------------------------
        | RATA-RATA ORDER
        |--------------------------------------------------------------------------
        */
        $rataRataOrder = $transaksiBulanIni > 0
            ? $omzetBulanIni / $transaksiBulanIni
            : 0;


        /*
        |--------------------------------------------------------------------------
        | GRAFIK HARIAN
        |--------------------------------------------------------------------------
        */
        $dailyTrend = $this->dailySalesTrend(
            $startOfMonth,
            $endOfMonth
        );


        /*
        |--------------------------------------------------------------------------
        | METODE PEMBAYARAN
        |--------------------------------------------------------------------------
        */
        $metodePembayaran = DB::table('payments')
            ->where('status', 'Berhasil')
            ->whereBetween('paid_at', [
                $startOfMonth,
                $endOfMonth
            ])
            ->selectRaw(
                'payment_method, COUNT(*) as jumlah'
            )
            ->groupBy('payment_method')
            ->pluck('jumlah', 'payment_method');


        $totalMetode = $metodePembayaran->sum();


        /*
        |--------------------------------------------------------------------------
        | RIWAYAT PENJUALAN
        |--------------------------------------------------------------------------
        */
        $riwayatPenjualan = Order::with('payment')
            ->whereHas('payment', function ($query) use (
                $startOfMonth,
                $endOfMonth
            ) {
                $query
                    ->where('status', 'Berhasil')
                    ->whereBetween('paid_at', [
                        $startOfMonth,
                        $endOfMonth
                    ]);
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
            'selectedDate',
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
        [
            $startOfMonth,
            $endOfMonth,
            $selectedMonth
        ] = $this->resolvePeriod($request);


        return Excel::download(
            new SalesExport(
                $startOfMonth,
                $endOfMonth
            ),
            'laporan-penjualan-' . $selectedMonth . '.xlsx'
        );
    }


    /**
     * ============================================================
     * EXPORT PDF
     * ============================================================
     */
    public function exportSalesPdf(Request $request): Response
    {
        [
            $startOfMonth,
            $endOfMonth,
            $selectedMonth
        ] = $this->resolvePeriod($request);


        /*
        |--------------------------------------------------------------------------
        | OMZET
        |--------------------------------------------------------------------------
        */
        $omzetBulanIni = DB::table('payments')
            ->where('status', 'Berhasil')
            ->whereBetween('paid_at', [
                $startOfMonth,
                $endOfMonth
            ])
            ->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | TRANSAKSI
        |--------------------------------------------------------------------------
        */
        $orders = Order::with('payment')
            ->whereHas('payment', function ($query) use (
                $startOfMonth,
                $endOfMonth
            ) {
                $query
                    ->where('status', 'Berhasil')
                    ->whereBetween('paid_at', [
                        $startOfMonth,
                        $endOfMonth
                    ]);
            })
            ->latest()
            ->get();


        $transaksiBulanIni = $orders->count();


        $periodLabel = $startOfMonth
            ->locale('id')
            ->translatedFormat('F Y');


        $pdf = Pdf::loadView(
            'owner.sales-pdf',
            compact(
                'omzetBulanIni',
                'transaksiBulanIni',
                'orders',
                'periodLabel'
            )
        );


        return $pdf->download(
            'laporan-penjualan-' . $selectedMonth . '.pdf'
        );
    }


    /**
     * ============================================================
     * RESOLVE PERIOD
     * ============================================================
     */
    private function resolvePeriod(Request $request): array
    {
        $selectedDate = $request->get(
            'tanggal',
            now()->format('Y-m-d')
        );


        try {

            $date = Carbon::createFromFormat(
                'Y-m-d',
                $selectedDate
            );

        } catch (\Exception $e) {

            $date = now();

            $selectedDate = $date->format('Y-m-d');
        }


        $selectedMonth = $date->format('Y-m');


        return [
            $date->copy()->startOfMonth(),
            $date->copy()->endOfMonth(),
            $selectedMonth,
            $selectedDate,
        ];
    }


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

        $cursor = $start->copy();


        while ($cursor->lte($end)) {

            $total = DB::table('payments')
                ->where('status', 'Berhasil')
                ->whereDate(
                    'paid_at',
                    $cursor->toDateString()
                )
                ->sum('amount');


            $trend->push([
                'label' => $cursor->translatedFormat('d M'),
                'total' => (float) $total,
            ]);


            $cursor->addDay();
        }


        return $trend;
    }


}

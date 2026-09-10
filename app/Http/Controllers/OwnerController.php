<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
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
    public function dashboard(): View
    {
        $today = now()->toDateString();

        /*
        |--------------------------------------------------------------------------
        | PENJUALAN HARI INI
        |--------------------------------------------------------------------------
        | Hanya pembayaran yang benar-benar berhasil.
        */
        $penjualanHariIni = DB::table('payments')
            ->whereDate('paid_at', $today)
            ->where('status', 'Berhasil')
            ->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | TOTAL TRANSAKSI HARI INI
        |--------------------------------------------------------------------------
        */
        $transaksiHariIni = DB::table('payments')
            ->whereDate('paid_at', $today)
            ->where('status', 'Berhasil')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | PELANGGAN BARU HARI INI
        |--------------------------------------------------------------------------
        */
        $pelangganBaruHariIni = User::where('role', 'customer')
            ->whereDate('created_at', $today)
            ->count();


        /*
        |--------------------------------------------------------------------------
        | MENUNGGU KITCHEN
        |--------------------------------------------------------------------------
        | Sudah dibayar tetapi belum mulai dibuat.
        */
        $pesananMenunggu = Order::where('payment_status', 'Dibayar')
            ->where('status', 'Menunggu')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | SEDANG DIPROSES
        |--------------------------------------------------------------------------
        */
        $pesananDiproses = Order::where('payment_status', 'Dibayar')
            ->where('status', 'Diproses')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | SIAP DIAMBIL
        |--------------------------------------------------------------------------
        | Kitchen sudah selesai membuat pesanan.
        */
        $pesananSelesai = Order::where('payment_status', 'Dibayar')
            ->where('status', 'Selesai')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | MENU AKTIF
        |--------------------------------------------------------------------------
        */
        $menuAktif = Product::where('is_available', true)
            ->count();


        /*
        |--------------------------------------------------------------------------
        | GRAFIK PENJUALAN 7 HARI
        |--------------------------------------------------------------------------
        */
        $weeklyTrend = $this->weeklySalesTrend();


        /*
        |--------------------------------------------------------------------------
        | TRANSAKSI TERBARU
        |--------------------------------------------------------------------------
        */
        $transaksiTerbaru = Order::with('payment')
            ->whereHas('payment', function ($query) {
                $query->where('status', 'Berhasil');
            })
            ->latest()
            ->limit(5)
            ->get();


        return view('owner.dashboard', compact(
            'penjualanHariIni',
            'transaksiHariIni',
            'pelangganBaruHariIni',
            'pesananMenunggu',
            'pesananDiproses',
            'pesananSelesai',
            'menuAktif',
            'weeklyTrend',
            'transaksiTerbaru'
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
            'selectedDate'
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
                'label' => $cursor->format('d'),
                'total' => (float) $total,
            ]);


            $cursor->addDay();
        }


        return $trend;
    }


    /**
     * ============================================================
     * WEEKLY SALES TREND
     * ============================================================
     */
    private function weeklySalesTrend()
    {
        $trend = collect();


        for ($i = 6; $i >= 0; $i--) {

            $date = now()->subDays($i);


            $total = DB::table('payments')
                ->where('status', 'Berhasil')
                ->whereDate(
                    'paid_at',
                    $date->toDateString()
                )
                ->sum('amount');


            $trend->push([
                'label' => $date
                    ->locale('id')
                    ->translatedFormat('D'),

                'total' => (float) $total,
            ]);
        }


        return $trend;
    }
}
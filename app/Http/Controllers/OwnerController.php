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
    public function dashboard(): View
    {
        $today = now()->toDateString();

        $penjualanHariIni = Order::where('payment_status', 'Dibayar')
            ->whereDate('created_at', $today)
            ->sum('total');

        $transaksiHariIni = Order::where('payment_status', 'Dibayar')
            ->whereDate('created_at', $today)
            ->count();

        $pelangganBaruHariIni = User::where('role', 'customer')
            ->whereDate('created_at', $today)
            ->count();

        $pesananSelesai = Order::where('status', 'Selesai')
            ->whereDate('created_at', $today)
            ->count();

        $pesananDiproses = Order::whereIn('status', ['Menunggu', 'Diproses'])
            ->whereDate('created_at', $today)
            ->count();

        $menuAktif = Product::where('is_available', true)->count();

        $weeklyTrend = $this->weeklySalesTrend();

        $transaksiTerbaru = Order::latest()->limit(3)->get();

        return view('owner.dashboard', compact(
            'penjualanHariIni',
            'transaksiHariIni',
            'pelangganBaruHariIni',
            'pesananSelesai',
            'pesananDiproses',
            'menuAktif',
            'weeklyTrend',
            'transaksiTerbaru'
        ));
    }

    public function sales(Request $request): View
    {
        [$start, $end, $dariInput, $sampaiInput] = $this->resolvePeriod($request);

        $omzetBulanIni = Order::where('payment_status', 'Dibayar')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total');

        $transaksiBulanIni = Order::where('payment_status', 'Dibayar')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $rataRataOrder = $transaksiBulanIni > 0
            ? $omzetBulanIni / $transaksiBulanIni
            : 0;

        $dailyTrend = $this->dailySalesTrend($start, $end);

        $metodePembayaran = DB::table('payments')
            ->join('orders', 'orders.id', '=', 'payments.order_id')
            ->where('payments.status', 'Berhasil')
            ->whereBetween('orders.created_at', [$start, $end])
            ->selectRaw('payments.payment_method, COUNT(*) as jumlah')
            ->groupBy('payments.payment_method')
            ->pluck('jumlah', 'payment_method');

        $totalMetode = $metodePembayaran->sum();

        $riwayatPenjualan = Order::whereBetween('created_at', [$start, $end])
            ->latest()
            ->limit(50)
            ->get();

        return view('owner.sales', compact(
            'omzetBulanIni',
            'transaksiBulanIni',
            'rataRataOrder',
            'dailyTrend',
            'metodePembayaran',
            'totalMetode',
            'riwayatPenjualan',
            'dariInput',
            'sampaiInput'
        ));
    }

    public function exportSalesExcel(Request $request)
    {
        [$start, $end, $dariInput, $sampaiInput] = $this->resolvePeriod($request);

        return Excel::download(
            new SalesExport($start, $end),
            'laporan-penjualan-' . $dariInput . '_sd_' . $sampaiInput . '.xlsx'
        );
    }

    public function exportSalesPdf(Request $request): Response
    {
        [$start, $end, $dariInput, $sampaiInput] = $this->resolvePeriod($request);

        $omzetBulanIni = Order::where('payment_status', 'Dibayar')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total');

        $transaksiBulanIni = Order::where('payment_status', 'Dibayar')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $orders = Order::where('payment_status', 'Dibayar')
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        $periodLabel = $start->translatedFormat('d F Y') . ' - ' . $end->translatedFormat('d F Y');

        $pdf = Pdf::loadView('owner.sales-pdf', compact('omzetBulanIni', 'transaksiBulanIni', 'orders', 'periodLabel'));

        return $pdf->download('laporan-penjualan-' . $dariInput . '_sd_' . $sampaiInput . '.pdf');
    }

    /**
     * Menentukan rentang tanggal dari input user (?dari=Y-m-d&sampai=Y-m-d).
     * Default: awal bulan ini sampai hari ini, kalau tidak diisi / format salah.
     */
    private function resolvePeriod(Request $request): array
    {
        $dariInput   = $request->get('dari');
        $sampaiInput = $request->get('sampai');

        try {
            $start = $dariInput ? Carbon::createFromFormat('Y-m-d', $dariInput)->startOfDay() : now()->startOfMonth();
        } catch (\Exception $e) {
            $start = now()->startOfMonth();
        }

        try {
            $end = $sampaiInput ? Carbon::createFromFormat('Y-m-d', $sampaiInput)->endOfDay() : now()->endOfDay();
        } catch (\Exception $e) {
            $end = now()->endOfDay();
        }

        // Kalau user membalik tanggal (dari > sampai), tukar otomatis supaya tidak error.
        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end, $start->format('Y-m-d'), $end->format('Y-m-d')];
    }

    /**
     * Total penjualan per hari, sepanjang rentang tanggal yang dipilih.
     */
    private function dailySalesTrend(Carbon $start, Carbon $end)
    {
        $trend = collect();
        $cursor = $start->copy()->startOfDay();
        $limit = $cursor->copy()->addDays(60); // batas aman biar tidak infinite loop kalau rentang kelewat panjang

        while ($cursor->lte($end) && $cursor->lte($limit)) {
            $total = Order::where('payment_status', 'Dibayar')
                ->whereDate('created_at', $cursor->toDateString())
                ->sum('total');

            $trend->push([
                'label' => $cursor->format('d/m'),
                'total' => $total,
            ]);

            $cursor->addDay();
        }

        return $trend;
    }

    /**
     * Dipakai khusus oleh Dashboard: tren 7 hari terakhir (real-time, tidak terpengaruh filter).
     */
    private function weeklySalesTrend()
    {
        $trend = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);

            $total = Order::where('payment_status', 'Dibayar')
                ->whereDate('created_at', $date->toDateString())
                ->sum('total');

            $trend->push([
                'label' => $date->translatedFormat('D'),
                'total' => $total,
            ]);
        }

        return $trend;
    }
}
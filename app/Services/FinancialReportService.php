<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    private const BULAN = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    public function summary(Carbon $start, Carbon $end): array
    {
        $pendapatan = (float) DB::table('payments')
            ->where('status', 'Berhasil')
            ->whereBetween('paid_at', [$start, $end])
            ->sum('amount');

        $costExpr = 'COALESCE(NULLIF(order_items.cost_price, 0), products.cost_price, 0)';

        $paidItems = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->whereExists(function ($query) use ($start, $end) {
                $query->selectRaw('1')
                    ->from('payments')
                    ->whereColumn('payments.order_id', 'orders.id')
                    ->where('payments.status', 'Berhasil')
                    ->whereBetween('payments.paid_at', [$start, $end]);
            });

        $hpp = (float) (clone $paidItems)->sum(DB::raw("order_items.quantity * {$costExpr}"));

        $itemsWithoutCost = (int) (clone $paidItems)
            ->whereRaw("{$costExpr} <= 0")
            ->count();

        $range = [$start->toDateString(), $end->toDateString()];

        // Catatan keuangan manual: pengeluaran = biaya operasional, pemasukan = pemasukan lain
        $biayaOperasional = (float) Expense::pengeluaran()->whereBetween('expense_date', $range)->sum('amount');
        $pemasukanLain = (float) Expense::pemasukan()->whereBetween('expense_date', $range)->sum('amount');

        $labaKotor = $pendapatan - $hpp;
        $labaOperasional = $labaKotor - $biayaOperasional;
        $labaBersih = $labaOperasional + $pemasukanLain;

        // Margin laba bersih dibandingkan dengan pendapatan penjualan.
        $margin = $pendapatan > 0 ? round(($labaBersih / $pendapatan) * 100, 1) : 0.0;

        return [
            'pendapatan' => $pendapatan,
            'hpp' => $hpp,
            'laba_kotor' => $labaKotor,
            'biaya_operasional' => $biayaOperasional,
            'laba_operasional' => $labaOperasional,
            'pemasukan_lain' => $pemasukanLain,
            'laba_bersih' => $labaBersih,
            'margin' => $margin,
            'has_sales' => $pendapatan > 0,
            'items_without_cost' => $itemsWithoutCost,
        ];
    }

    public function topProducts(Carbon $start, Carbon $end, int $limit = 20): Collection
    {
        $costExpr = 'COALESCE(NULLIF(order_items.cost_price, 0), products.cost_price, 0)';

        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->whereExists(function ($query) use ($start, $end) {
                $query->selectRaw('1')
                    ->from('payments')
                    ->whereColumn('payments.order_id', 'orders.id')
                    ->where('payments.status', 'Berhasil')
                    ->whereBetween('payments.paid_at', [$start, $end]);
            })
            ->selectRaw("order_items.product_name, SUM(order_items.quantity) as total_qty, SUM(order_items.quantity * order_items.price) as total_sales, SUM(order_items.quantity * {$costExpr}) as total_cost, SUM(order_items.quantity * (order_items.price - {$costExpr})) as total_profit")
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }

    public function topCustomers(Carbon $start, Carbon $end, int $limit = 10): Collection
    {
        return DB::table('orders')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->where('payments.status', 'Berhasil')
            ->whereBetween('payments.paid_at', [$start, $end])
            ->selectRaw('orders.user_id, orders.customer_name, COUNT(DISTINCT orders.id) as total_orders, SUM(payments.amount) as total_spent')
            ->groupBy('orders.user_id', 'orders.customer_name')
            ->orderByDesc('total_orders')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get();
    }

    public function expensesByCategory(Carbon $start, Carbon $end): Collection
    {
        return Expense::pengeluaran()
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
    }

    public static function resolvePeriod(?string $month = null, ?string $from = null, ?string $to = null): array
    {
        if ($from || $to) {
            try {
                $start = $from ? Carbon::createFromFormat('Y-m-d', $from)->startOfDay() : null;
                $end = $to ? Carbon::createFromFormat('Y-m-d', $to)->endOfDay() : null;

                $start ??= $end->copy()->startOfDay();
                $end ??= $start->copy()->endOfDay();

                if ($start->gt($end)) {
                    [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
                }

                return [$start, $end];
            } catch (\Throwable) {
                // format salah -> lanjut ke bulan
            }
        }

        try {
            $start = $month
                ? Carbon::createFromFormat('Y-m', $month)->startOfMonth()
                : now()->startOfMonth();
        } catch (\Throwable) {
            $start = now()->startOfMonth();
        }

        return [$start, $start->copy()->endOfMonth()];
    }

    public static function periodLabel(Carbon $start, Carbon $end): string
    {
        $fmt = fn (Carbon $d) => $d->day . ' ' . self::BULAN[$d->month] . ' ' . $d->year;

        $fullMonth = $start->isSameDay($start->copy()->startOfMonth())
            && $end->isSameDay($start->copy()->endOfMonth());

        if ($fullMonth) {
            return self::BULAN[$start->month] . ' ' . $start->year;
        }

        if ($start->isSameDay($end)) {
            return $fmt($start);
        }

        return $fmt($start) . ' - ' . $fmt($end);
    }
}

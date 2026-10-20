<?php

namespace App\Exports;

use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected Carbon $start;
    protected Carbon $end;

    public function __construct(Carbon $start, Carbon $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection(): Enumerable
    {
        $costExpr = 'COALESCE(NULLIF(order_items.cost_price, 0), products.cost_price, 0)';

        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->where('payments.status', 'Berhasil')
            ->whereBetween('payments.paid_at', [$this->start, $this->end])
            ->selectRaw("order_items.product_name, SUM(order_items.quantity) as total_qty, SUM(order_items.quantity * order_items.price) as total_pendapatan, SUM(order_items.quantity * {$costExpr}) as hpp")
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_qty')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Produk',
            'Jumlah Terjual',
            'Total Pendapatan (Rp)',
            'Harga Pokok Penjualan (Rp)',
            'Laba Kotor (Rp)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->product_name,
            $row->total_qty,
            number_format($row->total_pendapatan, 0, ',', '.'),
            number_format($row->hpp, 0, ',', '.'),
            number_format($row->total_pendapatan - $row->hpp, 0, ',', '.'),
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\OrderItem;
use Illuminate\Support\Carbon;
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
        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'Dibayar')
            ->whereBetween('orders.created_at', [$this->start, $this->end])
            ->selectRaw('order_items.product_name, SUM(order_items.quantity) as total_qty, SUM(order_items.quantity * order_items.price) as total_pendapatan')
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
        ];
    }

    public function map($row): array
    {
        return [
            $row->product_name,
            $row->total_qty,
            number_format($row->total_pendapatan, 0, ',', '.'),
        ];
    }
}
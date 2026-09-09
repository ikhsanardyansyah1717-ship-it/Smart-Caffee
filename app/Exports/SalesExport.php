<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromCollection, WithHeadings, WithMapping
{
    protected Carbon $start;
    protected Carbon $end;

    public function __construct(Carbon $start, Carbon $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        return Order::where('payment_status', 'Dibayar')
            ->whereBetween('created_at', [$this->start, $this->end])
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return ['No. Order', 'Tanggal', 'Pelanggan', 'Total', 'Status'];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->created_at->format('d-m-Y H:i'),
            $order->customer_name,
            $order->total,
            $order->payment_status,
        ];
    }
}
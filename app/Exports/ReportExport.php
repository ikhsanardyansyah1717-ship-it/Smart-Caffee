<?php

namespace App\Exports;

use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithStyles, WithTitle, WithEvents, ShouldAutoSize
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

    public function startCell(): string
    {
        return 'A5';
    }

    public function title(): string
    {
        return 'Penjualan Produk';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '713F12']], 'alignment' => ['vertical' => 'center']],
            2 => ['font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '713F12']]],
            3 => ['font' => ['size' => 10, 'color' => ['rgb' => '7C6A58']]],
            5 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '9A5B18']], 'alignment' => ['vertical' => 'center', 'wrapText' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', 'LAPORAN PENJUALAN · QUATTRO COFFEE');
                $sheet->mergeCells('A2:E2');
                $sheet->setCellValue('A2', 'Performa penjualan per produk');
                $sheet->mergeCells('A3:E3');
                $sheet->setCellValue('A3', 'Periode: ' . $this->start->format('d M Y') . ' – ' . $this->end->format('d M Y') . '   |   Dibuat: ' . now()->format('d M Y H:i'));
                $summary = app(\App\Services\FinancialReportService::class)->summary($this->start, $this->end);
                $sheet->mergeCells('G1:J1');
                $sheet->setCellValue('G1', 'RINGKASAN KEUANGAN');
                $sheet->mergeCells('G2:J2');
                $sheet->setCellValue('G2', 'Angka untuk periode laporan terpilih');
                $summaryRows = [
                    ['Total pendapatan', $summary['pendapatan']],
                    ['Harga pokok penjualan', $summary['hpp']],
                    ['Laba kotor', $summary['laba_kotor']],
                    ['Biaya operasional', $summary['biaya_operasional']],
                    ['Laba operasional', $summary['laba_operasional']],
                    ['Pemasukan lain', $summary['pemasukan_lain']],
                    ['Laba bersih', $summary['laba_bersih']],
                    ['Margin laba bersih', $summary['margin'] / 100],
                ];
                foreach ($summaryRows as $index => [$label, $value]) {
                    $row = $index + 4;
                    $sheet->setCellValue("G{$row}", $label);
                    $sheet->setCellValue("H{$row}", $value);
                    $sheet->mergeCells("H{$row}:J{$row}");
                }
                $sheet->getStyle('G1:J1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '713F12']],
                    'alignment' => ['vertical' => 'center'],
                ]);
                $sheet->getStyle('G2:J2')->getFont()->getColor()->setRGB('7C6A58');
                $sheet->getStyle('G4:J11')->getBorders()->getBottom()->setBorderStyle('thin')->getColor()->setRGB('EADCCB');
                $sheet->getStyle('H4:H10')->getNumberFormat()->setFormatCode('"Rp" #,##0;[Red]("Rp" #,##0)');
                $sheet->getStyle('H11')->getNumberFormat()->setFormatCode('0.0%');
                $sheet->getStyle('G10:J10')->getFont()->applyFromArray(['bold' => true, 'color' => ['rgb' => '713F12']]);
                $sheet->getStyle('H4:J11')->getAlignment()->setHorizontal('right');
                $sheet->getRowDimension(1)->setRowHeight(32);
                $sheet->getRowDimension(5)->setRowHeight(30);
                $lastRow = max(5, $sheet->getHighestRow());
                $sheet->freezePane('A6');
                $sheet->setAutoFilter("A5:E{$lastRow}");
                if ($lastRow > 5) {
                    $sheet->getStyle("A6:E{$lastRow}")->getBorders()->getBottom()->setBorderStyle('thin')->getColor()->setRGB('EADCCB');
                    $sheet->getStyle("C6:E{$lastRow}")->getNumberFormat()->setFormatCode('#,##0;[Red](#,##0)');
                    for ($row = 6; $row <= $lastRow; $row += 2) {
                        $sheet->getStyle("A{$row}:E{$row}")->getFill()->setFillType('solid')->getStartColor()->setRGB('FBF7F1');
                    }
                }
                $sheet->getStyle("A1:E{$lastRow}")->getAlignment()->setVertical('center');
            },
        ];
    }

    public function map($row): array
    {
        return [
            $row->product_name,
            $row->total_qty,
            (float) $row->total_pendapatan,
            (float) $row->hpp,
            (float) ($row->total_pendapatan - $row->hpp),
        ];
    }
}

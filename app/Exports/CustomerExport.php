<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private readonly Collection $customers)
    {
    }

    public function collection(): Collection
    {
        return $this->customers;
    }

    public function headings(): array
    {
        return ['Nama Pelanggan', 'Email', 'Nomor HP', 'Jumlah Pesanan', 'Tanggal Bergabung'];
    }

    public function map($customer): array
    {
        return [
            $customer->name,
            $customer->email,
            (string) ($customer->phone ?? ''),
            $customer->orders_count,
            $customer->created_at?->format('d-m-Y H:i'),
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::updateOrCreate(
            ['code' => 'BJR-001'],
            [
                'name' => 'Banjar 1',
                'address' => 'Koordinat dummy: -7.356824, 108.635700',
                'latitude' => -7.356824,
                'longitude' => 108.635700,
                'is_active' => true,
            ]
        );

        Branch::updateOrCreate(
            ['code' => 'BDG-001'],
            [
                'name' => 'Bandung 1',
                'address' => 'Jl. Batusari Komplek Buana Citra Ciwastra No. D-3, Buahbatu, Kec. Bojongsoang, Kabupaten Bandung, Jawa Barat 40287',
                // Koordinat ini hanya titik perkiraan sementara di area alamat.
                // Ganti dengan koordinat pin Google Maps yang tepat saat tersedia.
                'latitude' => -6.9825000,
                'longitude' => 107.6680000,
                'is_active' => true,
            ]
        );
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('Menunggu','Diproses','Selesai','Sudah Diambil','Dibatalkan') NOT NULL DEFAULT 'Menunggu'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            DB::statement("UPDATE orders SET status = 'Selesai' WHERE status = 'Sudah Diambil'");
            DB::statement("ALTER TABLE orders MODIFY status ENUM('Menunggu','Diproses','Selesai','Dibatalkan') NOT NULL DEFAULT 'Menunggu'");
        }
    }
};

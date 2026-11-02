<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE payments AS p
            INNER JOIN orders AS o ON o.id = p.order_id
            SET p.status = 'Berhasil',
                p.paid_at = COALESCE(p.paid_at, o.updated_at, o.created_at),
                p.updated_at = NOW()
            WHERE o.payment_status = 'Dibayar'
              AND o.status <> 'Dibatalkan'
              AND (p.status <> 'Berhasil' OR p.paid_at IS NULL)");

        DB::statement("UPDATE orders AS o
            INNER JOIN payments AS p ON p.order_id = o.id
            SET o.payment_status = 'Dibayar',
                o.updated_at = NOW()
            WHERE p.status = 'Berhasil'
              AND o.payment_status <> 'Dibayar'
              AND o.status <> 'Dibatalkan'");
    }

    public function down(): void
    {
        // Keep reconciled payment history intact when rolling back this data migration.
    }
};

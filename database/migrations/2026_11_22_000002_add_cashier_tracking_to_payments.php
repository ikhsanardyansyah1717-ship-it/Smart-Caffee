<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'cashier_id')) {
                $table->foreignId('cashier_id')->nullable()->after('order_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('payments', 'cashier_name')) {
                $table->string('cashier_name')->nullable()->after('cashier_id');
            }
        });

        DB::table('payments')
            ->join('orders', 'orders.id', '=', 'payments.order_id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->where('users.role', 'kasir')
            ->whereNull('payments.cashier_id')
            ->select('payments.id', 'users.id as user_id', 'users.name as user_name')
            ->orderBy('payments.id')
            ->get()
            ->each(function ($row) {
                DB::table('payments')->where('id', $row->id)->update([
                    'cashier_id' => $row->user_id,
                    'cashier_name' => $row->user_name,
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'cashier_id')) {
                $table->dropConstrainedForeignId('cashier_id');
            }
            if (Schema::hasColumn('payments', 'cashier_name')) {
                $table->dropColumn('cashier_name');
            }
        });
    }
};

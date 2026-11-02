<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('kitchen_confirmed_at')->nullable();
        });

        DB::table('orders')
            ->where('payment_status', 'Dibayar')
            ->whereIn('status', ['Menunggu', 'Diproses', 'Selesai', 'Sudah Diambil'])
            ->update(['kitchen_confirmed_at' => DB::raw('COALESCE(kitchen_started_at, created_at)')]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('kitchen_confirmed_at');
        });
    }
};

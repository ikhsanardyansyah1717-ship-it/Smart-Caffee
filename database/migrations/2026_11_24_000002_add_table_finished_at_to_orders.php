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
            $table->timestamp('table_finished_at')->nullable();
        });

        DB::table('orders')
            ->where('status', 'Sudah Diambil')
            ->whereNotNull('table_number')
            ->where('table_number', '!=', 'Take Away')
            ->whereNull('table_finished_at')
            ->update(['table_finished_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('table_finished_at');
        });
    }
};

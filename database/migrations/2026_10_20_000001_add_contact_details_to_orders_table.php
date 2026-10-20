<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone', 30)->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_phone');
            }
        });
    }

    public function down(): void
    {
        $columns = array_values(array_filter(
            ['customer_phone', 'customer_email'],
            fn (string $column) => Schema::hasColumn('orders', $column)
        ));

        if ($columns) {
            Schema::table('orders', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }
};

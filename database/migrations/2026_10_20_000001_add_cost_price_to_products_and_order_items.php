<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 2)->default(0)->after('price');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 2)->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('products', fn (Blueprint $t) => $t->dropColumn('cost_price'));
        Schema::table('order_items', fn (Blueprint $t) => $t->dropColumn('cost_price'));
    }
};
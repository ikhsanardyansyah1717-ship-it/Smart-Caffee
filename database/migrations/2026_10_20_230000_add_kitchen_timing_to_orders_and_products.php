<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('kitchen_started_at')->nullable();
            $table->timestamp('kitchen_completed_at')->nullable();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedSmallInteger('prep_minutes')->default(12);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('prep_minutes');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['kitchen_started_at', 'kitchen_completed_at']);
        });
    }
};

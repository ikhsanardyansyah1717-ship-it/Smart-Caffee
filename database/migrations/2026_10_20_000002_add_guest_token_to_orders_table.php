<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'guest_token')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('guest_token', 64)->nullable()->index()->after('customer_email');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'guest_token')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('guest_token');
            });
        }
    }
};

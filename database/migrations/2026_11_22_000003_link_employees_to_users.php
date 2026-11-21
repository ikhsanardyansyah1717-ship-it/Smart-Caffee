<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('employees', 'user_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('employees', 'user_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }
    }
};

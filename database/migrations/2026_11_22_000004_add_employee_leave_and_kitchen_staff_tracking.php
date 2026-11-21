<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->string('status', 20)->default('Pending')->index();
            $table->text('owner_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'start_date', 'end_date']);
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'kitchen_started_by')) {
                $table->foreignId('kitchen_started_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'kitchen_completed_by')) {
                $table->foreignId('kitchen_completed_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'kitchen_completed_by')) {
                $table->dropConstrainedForeignId('kitchen_completed_by');
            }
            if (Schema::hasColumn('orders', 'kitchen_started_by')) {
                $table->dropConstrainedForeignId('kitchen_started_by');
            }
        });

        Schema::dropIfExists('employee_leave_requests');
    }
};

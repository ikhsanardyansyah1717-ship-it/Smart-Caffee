<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_salary_id')->constrained()->restrictOnDelete();
            $table->foreignId('expense_id')->unique()->nullable()->constrained()->nullOnDelete();
            $table->string('period', 7);
            $table->decimal('amount', 12, 2);
            $table->date('paid_on');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'period']);
            $table->index('paid_on');
        });
    }

    public function down(): void { Schema::dropIfExists('employee_salary_payments'); }
};

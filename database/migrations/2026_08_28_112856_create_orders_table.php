<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('customer_name');

            $table->string('table_number')->nullable();

            $table->decimal('subtotal', 12, 2)->default(0);

            $table->decimal('tax', 12, 2)->default(0);

            $table->decimal('total', 12, 2)->default(0);

            $table->enum('status', [
                'Menunggu',
                'Diproses',
                'Selesai',
                'Dibatalkan'
            ])->default('Menunggu');

            $table->enum('payment_status', [
                'Belum Dibayar',
                'Dibayar'
            ])->default('Belum Dibayar');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
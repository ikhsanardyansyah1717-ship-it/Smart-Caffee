<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropUnique('product_reviews_product_id_user_id_unique');
            $table->dropUnique('product_reviews_product_id_guest_token_unique');
            $table->foreignId('order_item_id')->nullable()->unique()->after('product_id')
                ->constrained('order_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropForeign(['order_item_id']);
            $table->dropUnique(['order_item_id']);
            $table->dropColumn('order_item_id');
            $table->unique(['product_id', 'user_id']);
            $table->unique(['product_id', 'guest_token']);
        });
    }
};

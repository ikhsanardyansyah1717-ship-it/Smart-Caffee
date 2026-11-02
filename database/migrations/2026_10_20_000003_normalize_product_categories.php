<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')->where('category', 'Makanan Berat')->update(['category' => 'Food']);
    }

    public function down(): void
    {
        // Keep normalized product data intact when rolling back this data-only migration.
    }
};

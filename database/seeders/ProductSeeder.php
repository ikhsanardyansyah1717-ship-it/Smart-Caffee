<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Cappuccino',
            'category' => 'Coffee',
            'price' => 28000,
            'icon' => 'fa-mug-hot',
            'description' => 'Espresso dengan steamed milk dan foam lembut.',
            'is_available' => true,
        ]);

        Product::create([
            'name' => 'Cafe Latte',
            'category' => 'Coffee',
            'price' => 30000,
            'icon' => 'fa-mug-saucer',
            'description' => 'Espresso dengan susu creamy yang lembut.',
            'is_available' => true,
        ]);

        Product::create([
            'name' => 'Americano',
            'category' => 'Coffee',
            'price' => 22000,
            'icon' => 'fa-coffee',
            'description' => 'Espresso dengan air panas.',
            'is_available' => true,
        ]);

        Product::create([
            'name' => 'Caramel Macchiato',
            'category' => 'Coffee',
            'price' => 35000,
            'icon' => 'fa-glass-water',
            'description' => 'Kopi creamy dengan rasa caramel.',
            'is_available' => true,
        ]);

        Product::create([
            'name' => 'Matcha Latte',
            'category' => 'Non Coffee',
            'price' => 32000,
            'icon' => 'fa-leaf',
            'description' => 'Matcha premium dengan susu creamy.',
            'is_available' => true,
        ]);

        Product::create([
            'name' => 'Chocolate',
            'category' => 'Non Coffee',
            'price' => 30000,
            'icon' => 'fa-chocolate-bar',
            'description' => 'Minuman cokelat creamy dan nikmat.',
            'is_available' => true,
        ]);

        Product::create([
            'name' => 'Croissant',
            'category' => 'Food',
            'price' => 24000,
            'icon' => 'fa-bread-slice',
            'description' => 'Croissant renyah dengan tekstur lembut.',
            'is_available' => true,
        ]);

        Product::create([
            'name' => 'French Fries',
            'category' => 'Food',
            'price' => 26000,
            'icon' => 'fa-bowl-food',
            'description' => 'Kentang goreng renyah dan gurih.',
            'is_available' => true,
        ]);
    }
}
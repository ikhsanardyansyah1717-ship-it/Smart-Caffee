<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['Cappuccino', 'Coffee', 28000, 11000, 'fa-mug-hot', 'Espresso dengan steamed milk dan foam lembut.'],
            ['Cafe Latte', 'Coffee', 30000, 12000, 'fa-mug-saucer', 'Espresso dengan susu creamy yang lembut.'],
            ['Americano', 'Coffee', 22000, 7000, 'fa-mug-hot', 'Espresso dengan air panas.'],
            ['Caramel Macchiato', 'Coffee', 35000, 14000, 'fa-glass-water', 'Kopi creamy dengan rasa caramel.'],
            ['Kopi Susu Gula Aren', 'Coffee', 28000, 11000, 'fa-mug-hot', 'Espresso, susu, dan manis legit gula aren.'],
            ['Spanish Latte', 'Coffee', 32000, 13000, 'fa-mug-saucer', 'Espresso dengan susu creamy dan sentuhan manis.'],
            ['Matcha Latte', 'Non Coffee', 32000, 13000, 'fa-leaf', 'Matcha premium dengan susu creamy.'],
            ['Chocolate', 'Non Coffee', 30000, 12000, 'fa-mug-hot', 'Minuman cokelat creamy dan nikmat.'],
            ['Red Velvet Latte', 'Non Coffee', 30000, 13000, 'fa-mug-hot', 'Minuman red velvet lembut dengan susu.'],
            ['Taro Latte', 'Non Coffee', 30000, 12000, 'fa-glass-water', 'Taro manis creamy dengan susu.'],
            ['Lemon Tea', 'Non Coffee', 22000, 7000, 'fa-glass-water', 'Teh segar dengan lemon.'],
            ['Spaghetti Bolognese', 'Food', 35000, 16000, 'fa-bowl-food', 'Pasta dengan saus bolognese gurih dan taburan keju.'],
            ['Chicken Katsu Rice', 'Food', 38000, 19000, 'fa-bowl-rice', 'Nasi hangat dengan chicken katsu renyah dan saus pilihan.'],
            ['Nasi Goreng Spesial', 'Food', 32000, 15000, 'fa-bowl-rice', 'Nasi goreng gurih dengan telur dan pelengkap.'],
            ['French Fries', 'Snack', 26000, 10000, 'fa-bowl-food', 'Kentang goreng renyah dan gurih.'],
            ['Onion Rings', 'Snack', 22000, 9000, 'fa-bowl-food', 'Irisan bawang berbalut tepung renyah.'],
            ['Chicken Wings', 'Snack', 32000, 15000, 'fa-drumstick-bite', 'Sayap ayam berbumbu dengan pilihan saus.'],
            ['Croissant', 'Bakery', 24000, 10000, 'fa-bread-slice', 'Croissant renyah dengan tekstur lembut.'],
            ['Chocolate Croissant', 'Bakery', 28000, 12000, 'fa-bread-slice', 'Pastry berlapis renyah dengan isian cokelat.'],
            ['Cinnamon Roll', 'Bakery', 27000, 11000, 'fa-bread-slice', 'Roti gulung lembut dengan kayu manis dan glaze.'],
            ['Brownies Cokelat', 'Dessert', 28000, 12000, 'fa-cookie-bite', 'Brownies cokelat lembut untuk hidangan penutup.'],
            ['Cheesecake', 'Dessert', 35000, 16000, 'fa-cake-candles', 'Cheesecake lembut dengan rasa keju creamy.'],
            ['Waffle Ice Cream', 'Dessert', 32000, 14500, 'fa-ice-cream', 'Waffle hangat disajikan dengan es krim.'],
        ];

        foreach ($menus as [$name, $category, $price, $costPrice, $icon, $description]) {
            Product::firstOrCreate(
                ['name' => $name],
                [
                    'category' => $category,
                    'price' => $price,
                    'cost_price' => $costPrice,
                    'stock' => 15,
                    'icon' => $icon,
                    'description' => $description,
                    'is_available' => true,
                ]
            );
        }

        // Koreksi kategori produk lama jika sebelumnya dimasukkan ke kategori makanan berat.
        Product::where('name', 'Croissant')->update(['category' => 'Bakery']);
        Product::where('name', 'French Fries')->update(['category' => 'Snack']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KasirController extends Controller
{
    /**
     * Data produk sementara
     */
    private function products()
    {
        return [
            [
                'id' => 1,
                'name' => 'Cappuccino',
                'category' => 'Coffee',
                'price' => 28000,
                'icon' => 'fa-mug-hot'
            ],
            [
                'id' => 2,
                'name' => 'Cafe Latte',
                'category' => 'Coffee',
                'price' => 30000,
                'icon' => 'fa-mug-saucer'
            ],
            [
                'id' => 3,
                'name' => 'Americano',
                'category' => 'Coffee',
                'price' => 22000,
                'icon' => 'fa-coffee'
            ],
            [
                'id' => 4,
                'name' => 'Caramel Macchiato',
                'category' => 'Coffee',
                'price' => 35000,
                'icon' => 'fa-glass-water'
            ],
            [
                'id' => 5,
                'name' => 'Matcha Latte',
                'category' => 'Non Coffee',
                'price' => 32000,
                'icon' => 'fa-leaf'
            ],
            [
                'id' => 6,
                'name' => 'Chocolate',
                'category' => 'Non Coffee',
                'price' => 30000,
                'icon' => 'fa-chocolate-bar'
            ],
            [
                'id' => 7,
                'name' => 'Croissant',
                'category' => 'Food',
                'price' => 24000,
                'icon' => 'fa-bread-slice'
            ],
            [
                'id' => 8,
                'name' => 'French Fries',
                'category' => 'Food',
                'price' => 26000,
                'icon' => 'fa-bowl-food'
            ],
        ];
    }

    /**
     * Data pesanan sementara
     *
     * Namanya dibuat orderData()
     * agar tidak bentrok dengan method orders().
     */
    private function orderData()
    {
        return [
            [
                'id' => 'ORD-001',
                'customer' => 'Andi',
                'table' => 'A01',
                'items' => 'Cappuccino x2, Croissant x1',
                'total' => 80000,
                'status' => 'Selesai',
                'time' => '10:15'
            ],
            [
                'id' => 'ORD-002',
                'customer' => 'Budi',
                'table' => 'B03',
                'items' => 'Cafe Latte x1, French Fries x1',
                'total' => 56000,
                'status' => 'Diproses',
                'time' => '10:28'
            ],
            [
                'id' => 'ORD-003',
                'customer' => 'Citra',
                'table' => 'C02',
                'items' => 'Matcha Latte x1',
                'total' => 32000,
                'status' => 'Menunggu',
                'time' => '10:41'
            ],
            [
                'id' => 'ORD-004',
                'customer' => 'Dimas',
                'table' => 'Take Away',
                'items' => 'Americano x2',
                'total' => 44000,
                'status' => 'Selesai',
                'time' => '10:55'
            ],
        ];
    }

    /**
     * Dashboard Kasir
     */
    public function dashboard()
    {
        $orders = $this->orderData();

        return view('kasir.dashboard', [
            'orders' => $orders,
            'products' => $this->products(),
        ]);
    }

    /**
     * Halaman Pesanan
     */
    public function orders()
    {
        return view('kasir.orders', [
            'orders' => $this->orderData(),
            'products' => $this->products(),
        ]);
    }

    /**
     * Halaman Pembayaran
     */
    public function payment()
    {
        return view('kasir.payment', [
            'orders' => $this->orderData(),
        ]);
    }

    /**
     * Halaman Riwayat
     */
    public function history()
    {
        return view('kasir.history', [
            'orders' => $this->orderData(),
        ]);
    }

    /**
     * Simpan pesanan
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'customer' => 'required|string|max:100',
            'table' => 'required|string|max:50',
            'items' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        return redirect()
            ->route('kasir.orders')
            ->with('success', 'Pesanan berhasil dibuat.');
    }
}
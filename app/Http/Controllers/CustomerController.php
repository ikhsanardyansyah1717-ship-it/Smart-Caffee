<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Favorite;
use App\Models\Payment;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * =========================================================
     * HALAMAN HOME CUSTOMER
     * =========================================================
     *
     * Semua produk tetap ditampilkan.
     *
     * is_available = true  -> Tersedia
     * is_available = false -> Habis
     */
    public function home(): View
    {
        $products = Product::withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $favoriteIds = Auth::check()
            ? Favorite::where('user_id', Auth::id())->pluck('product_id')->map(fn ($id) => (int) $id)->all()
            : array_map('intval', session('guest_favorite_ids', []));

        return view(
            'customer.home',
            compact('products', 'favoriteIds')
        );
    }


    /**
     * =========================================================
     * HALAMAN ORDERS CUSTOMER
     * =========================================================
     */
    public function orders(): Response
    {
        $ordersQuery = Order::with(['items.review', 'payment']);
        if (Auth::check()) {
            $ordersQuery->where('user_id', Auth::id());
        } else {
            $guestToken = request()->cookie('quattro_guest_token')
                ?: $this->validGuestToken(request()->header('X-Guest-Token'));
            $guestOrderIds = session('guest_order_ids', []);

            if (!$guestToken && $guestOrderIds) {
                $guestToken = Str::random(64);
                Order::whereIn('id', $guestOrderIds)->update(['guest_token' => $guestToken]);
                $ordersQuery->where('guest_token', $guestToken);
            } else {
                if ($guestToken || $guestOrderIds) {
                    $ordersQuery->where(function ($query) use ($guestToken, $guestOrderIds) {
                        if ($guestToken) {
                            $query->where('guest_token', $guestToken);
                        }
                        if ($guestOrderIds) {
                            $guestToken ? $query->orWhereIn('id', $guestOrderIds) : $query->whereIn('id', $guestOrderIds);
                        }
                    });
                } else {
                    $ordersQuery->whereRaw('1 = 0');
                }
            }
        }
        $orders = $ordersQuery->latest()->get();
        $products = Product::orderBy('category')->orderBy('name')->get();
        $favoriteIds = Auth::check()
            ? Favorite::where('user_id', Auth::id())->pluck('product_id')->map(fn ($id) => (int) $id)->all()
            : array_map('intval', session('guest_favorite_ids', []));

        $response = response()->view(
            'customer.history',
            compact('orders', 'products', 'favoriteIds')
        );

        if (!Auth::check() && isset($guestToken) && !request()->cookie('quattro_guest_token')) {
            $response->cookie('quattro_guest_token', $guestToken, 525600, '/', null, request()->isSecure(), true, false, 'lax');
        }

        return $response;
    }

    /** Halaman ringkasan pesanan sebelum pembayaran. */
    public function checkoutPage(): View
    {
        $products = Product::orderBy('category')->orderBy('name')->get();

        return view('customer.checkout', compact('products'));
    }

    /** Halaman informasi pemesan dan pembayaran QRIS. */
    public function paymentPage(): View
    {
        $products = Product::orderBy('category')->orderBy('name')->get();

        return view('customer.payment', compact('products'));
    }

    public function productReviews(Product $product)
    {
        $reviews = $product->reviews()
            ->latest()
            ->limit(8)
            ->get(['id', 'customer_name', 'rating', 'comment', 'created_at']);

        return response()->json([
            'average' => round((float) $product->reviews()->avg('rating'), 1),
            'count' => $product->reviews()->count(),
            'reviews' => $reviews,
        ]);
    }

    public function storeProductReview(Request $request, Product $product)
    {
        $data = $request->validate([
            'order_item_id' => ['required', 'integer', 'exists:order_items,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        $orderItem = OrderItem::with('order')
            ->whereKey($data['order_item_id'])
            ->where('product_id', $product->id)
            ->firstOrFail();

        $guestToken = null;
        if (Auth::check()) {
            abort_unless((int) $orderItem->order->user_id === (int) Auth::id(), 403);
        } else {
            $providedToken = $request->cookie('quattro_guest_token')
                ?: $this->validGuestToken($request->header('X-Guest-Token'));
            $sessionOrderIds = array_map('intval', session('guest_order_ids', []));
            $tokenMatchesOrder = $providedToken
                && $orderItem->order->guest_token
                && hash_equals($orderItem->order->guest_token, $providedToken);

            abort_unless($tokenMatchesOrder || in_array((int) $orderItem->order_id, $sessionOrderIds, true), 403);

            $guestToken = $tokenMatchesOrder
                ? $providedToken
                : ($orderItem->order->guest_token ?: Str::random(64));

            if (!$orderItem->order->guest_token) {
                $orderItem->order->update(['guest_token' => $guestToken]);
            }
        }

        $reviewData = [
            'customer_name' => Auth::user()?->name ?? 'Pelanggan Quattro',
            'rating' => $data['rating'],
            'comment' => trim($data['comment']),
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'guest_token' => $guestToken,
        ];

        ProductReview::updateOrCreate(['order_item_id' => $orderItem->id], $reviewData);

        $response = response()->json([
            'message' => 'Terima kasih, ulasan menu berhasil disimpan.',
            'guest_token' => $guestToken,
            'average' => round((float) $product->reviews()->avg('rating'), 1),
            'count' => $product->reviews()->count(),
        ]);

        if (!Auth::check() && !request()->cookie('quattro_guest_token')) {
            $response->cookie('quattro_guest_token', $guestToken, 525600, '/', null, $request->isSecure(), true, false, 'lax');
        }

        return $response;
    }


    /**
     * =========================================================
     * HALAMAN FAVORITES
     * =========================================================
     *
     * Produk favorit tetap ditampilkan meskipun sedang habis.
     */
    public function favorites(): View
    {
        $favoriteIds = Auth::check()
            ? Favorite::where('user_id', Auth::id())->pluck('product_id')->map(fn ($id) => (int) $id)->all()
            : array_map('intval', session('guest_favorite_ids', []));

        $products = Product::whereIn('id', $favoriteIds)
            ->orderBy('name')
            ->get();

        return view(
            'customer.favorites',
            compact('products', 'favoriteIds')
        );
    }


    /**
     * =========================================================
     * HALAMAN PROFILE CUSTOMER
     * =========================================================
     */
    public function profile(): View
    {
        $user = Auth::user();
        if ($user && $user->role !== 'customer') {
            $user = null;
        }

        return view(
            'customer.profile',
            compact('user')
        );
    }


    /**
     * =========================================================
     * HALAMAN EDIT PROFILE
     * =========================================================
     */
    public function editProfile(): View
    {
        $user = Auth::user();

        return view(
            'customer.edit-profile',
            compact('user')
        );
    }


    /**
     * =========================================================
     * HALAMAN KEAMANAN & PASSWORD
     * =========================================================
     */
    public function security()
    {
        return view('customer.security');
    }


    /**
     * =========================================================
     * UPDATE PASSWORD CUSTOMER
     * =========================================================
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'current_password.required' =>
                'Password saat ini wajib diisi.',

            'current_password.current_password' =>
                'Password saat ini salah.',

            'password.required' =>
                'Password baru wajib diisi.',

            'password.min' =>
                'Password baru minimal 8 karakter.',

            'password.confirmed' =>
                'Konfirmasi password tidak cocok.',
        ]);

        $user->password = Hash::make(
            $data['password']
        );

        $user->save();

        return redirect()
            ->route('customer.security')
            ->with(
                'success',
                'Password berhasil diubah.'
            );
    }


    /**
     * =========================================================
     * UPDATE PROFILE CUSTOMER
     * =========================================================
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'username' => [
                'nullable',
                'string',
                'max:100',
                'unique:users,username,' . $user->id,
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
        ]);

        $user->name = $data['name'];
        $user->username = $data['username'] ?? null;
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;

        $user->save();

        return redirect()
            ->route('customer.profile')
            ->with(
                'success',
                'Profil berhasil diperbarui.'
            );
    }


    /**
     * =========================================================
     * HAPUS FOTO PROFILE
     * =========================================================
     */
    public function deleteAvatar(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->avatar) {

            if (
                Storage::disk('public')
                    ->exists($user->avatar)
            ) {
                Storage::disk('public')
                    ->delete($user->avatar);
            }

            $user->avatar = null;

            $user->save();
        }

        return redirect()
            ->route('customer.profile')
            ->with(
                'success',
                'Foto profil berhasil dihapus.'
            );
    }


    /**
     * =========================================================
     * TOGGLE FAVORITE
     * =========================================================
     */
    public function toggleFavorite(Request $request)
    {
        $data = $request->validate([
            'product_id' => [
                'required',
                'exists:products,id'
            ],
        ]);

        if (!Auth::check()) {
            $favoriteIds = array_map('intval', session('guest_favorite_ids', []));
            $isFavorite = in_array((int) $data['product_id'], $favoriteIds, true);
            $favoriteIds = $isFavorite
                ? array_values(array_diff($favoriteIds, [(int) $data['product_id']]))
                : [...$favoriteIds, (int) $data['product_id']];
            session(['guest_favorite_ids' => $favoriteIds]);

            return response()->json([
                'success' => true,
                'favorite' => !$isFavorite,
                'message' => $isFavorite ? 'Produk dihapus dari favorit.' : 'Produk ditambahkan ke favorit.',
            ]);
        }

        $favorite = Favorite::where(
                'user_id',
                Auth::id()
            )
            ->where(
                'product_id',
                $data['product_id']
            )
            ->first();

        if ($favorite) {

            $favorite->delete();

            return response()->json([
                'success' => true,
                'favorite' => false,
                'message' =>
                    'Produk dihapus dari favorit.',
            ]);
        }

        Favorite::create([
            'user_id' => Auth::id(),
            'product_id' => $data['product_id'],
        ]);

        $response = response()->json([
            'success' => true,
            'favorite' => true,
            'message' =>
                'Produk ditambahkan ke favorit.',
        ]);

        return $response;
    }


    /**
     * =========================================================
     * SIMPAN PESANAN CUSTOMER
     * =========================================================
     */
    public function storeOrder(Request $request)
    {
        $data = $request->validate([

            'customer_name' => [
                Auth::check() ? 'nullable' : 'required',
                'string',
                'max:255',
            ],

            'customer_phone' => ['nullable', 'string', 'max:30'],

            'customer_email' => ['nullable', 'email', 'max:255'],

            'table_number' => [
                'nullable',
                'string',
                'max:50'
            ],

            'items' => [
                'required',
                'array',
                'min:1'
            ],

            'items.*.product_id' => [
                'required',
                'exists:products,id'
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1'
            ],

            'items.*.customization' => [
                'nullable',
                'array:temperature,note',
            ],

            'items.*.customization.temperature' => ['nullable', 'in:ice,hot'],
            'items.*.customization.note' => ['nullable', 'string', 'max:300'],

            'payment_method' => [
                'required',
                'in:QRIS'
            ],
        ]);


        $guestToken = Auth::check()
            ? null
            : ($request->cookie('quattro_guest_token')
                ?: $this->validGuestToken($request->header('X-Guest-Token'))
                ?: $this->validGuestToken($request->input('guest_token'))
                ?: Str::random(64));

        $order = DB::transaction(function () use ($data, $guestToken) {

            $subtotal = 0;


            /**
             * =================================================
             * CEK PRODUK
             * =================================================
             *
             * Produk boleh tetap tampil di menu,
             * tetapi TIDAK boleh masuk order jika Habis.
             */
            foreach ($data['items'] as $item) {

                $product = Product::findOrFail(
                    $item['product_id']
                );

                if (!$product->is_available) {

                    abort(
                        422,
                        "Produk {$product->name} sedang habis. Silakan pilih produk lain."
                    );
                }

                $unitPrice = $this->configuredPrice(
                    $product,
                    $item['customization'] ?? []
                );

                $subtotal += $unitPrice * $item['quantity'];
            }


            /**
             * =================================================
             * PAJAK & TOTAL
             * =================================================
             */
            $tax = round(
                $subtotal * 0.10,
                2
            );

            $total = $subtotal + $tax;


            /**
             * =================================================
             * NOMOR ORDER
             * =================================================
             */
            $orderNumber =
                'ORD-' .
                now()->format('YmdHis') .
                '-' .
                random_int(100, 999);


            while (
                Order::where(
                    'order_number',
                    $orderNumber
                )->exists()
            ) {

                $orderNumber =
                    'ORD-' .
                    now()->format('YmdHis') .
                    '-' .
                    random_int(100, 999);
            }


            /**
             * =================================================
             * BUAT ORDER
             * =================================================
             */
            $order = Order::create([

                'order_number' =>
                    $orderNumber,

                'user_id' =>
                    Auth::id(),

                'customer_name' =>
                    Auth::user()?->name ?? trim($data['customer_name']),

                'customer_phone' => $data['customer_phone'] ?? Auth::user()?->phone,

                'customer_email' => $data['customer_email'] ?? Auth::user()?->email,

                'guest_token' => $guestToken,

                'table_number' =>
                    $data['table_number'] ?? null,

                'subtotal' =>
                    $subtotal,

                'tax' =>
                    $tax,

                'total' =>
                    $total,

                'status' =>
                    'Menunggu',

                'payment_status' =>
                    'Belum Dibayar',
            ]);


            /**
             * =================================================
             * ORDER ITEMS
             * =================================================
             */
            foreach ($data['items'] as $item) {

                /*
                 * CEK ULANG DI DALAM TRANSACTION
                 * supaya status terbaru dipakai.
                 */
                $product = Product::where(
                        'id',
                        (int) $item['product_id']
                    )
                    ->where(
                        'is_available',
                        true
                    )
                    ->first();

                if (!$product) {

                    throw new \RuntimeException(
                        'Produk sudah habis atau tidak tersedia. Silakan pilih produk lain.'
                    );
                }

                $quantity =
                    (int) $item['quantity'];

                $customization = $item['customization'] ?? [];
                $unitPrice = $this->configuredPrice($product, $customization);

                OrderItem::create([
                    'order_id' =>
                        $order->id,

                    'product_id' =>
                        $product->id,

                    'product_name' =>
                        $product->name,

                    'customization' =>
                        $customization,

                    'quantity' =>
                        $quantity,

                    'cost_price' =>
                        $product->cost_price,

                    'price' =>
                        $unitPrice,

                    'subtotal' =>
                        $unitPrice * $quantity,
                ]);
            }


            /**
             * =================================================
             * PAYMENT
             * =================================================
             */
            Payment::create([

                'order_id' =>
                    $order->id,

                'payment_method' =>
                    $data['payment_method'],

                'amount' =>
                    $total,

                'status' =>
                    'Pending',

                'paid_at' =>
                    null,
            ]);


            return $order;
        });

        if (!Auth::check()) {
            $guestOrderIds = session('guest_order_ids', []);
            $guestOrderIds[] = $order->id;
            session(['guest_order_ids' => array_values(array_unique($guestOrderIds))]);
        }


        $response = response()->json([

            'success' =>
                true,

            'message' =>
                'Transaksi berhasil dibuat.',

            'order_id' =>
                $order->id,

            'order_number' =>
                $order->order_number,

            'guest_token' =>
                $guestToken,

        ], 201);

        if ($guestToken) {
            $response->cookie('quattro_guest_token', $guestToken, 525600, '/', null, $request->isSecure(), true, false, 'lax');
        }

        return $response;
    }


    /**
     * =========================================================
     * ORDER HISTORY
     * =========================================================
     */
    public function orderHistory()
    {
        $ordersQuery = Order::with(['items.review', 'payment']);
        if (Auth::check()) {
            $ordersQuery->where('user_id', Auth::id());
        } else {
            $guestToken = request()->cookie('quattro_guest_token')
                ?: $this->validGuestToken(request()->header('X-Guest-Token'));
            $guestOrderIds = session('guest_order_ids', []);
            if ($guestToken || $guestOrderIds) {
                $ordersQuery->where(function ($query) use ($guestToken, $guestOrderIds) {
                    if ($guestToken) {
                        $query->where('guest_token', $guestToken);
                    }
                    if ($guestOrderIds) {
                        $guestToken ? $query->orWhereIn('id', $guestOrderIds) : $query->whereIn('id', $guestOrderIds);
                    }
                });
            } else {
                $ordersQuery->whereRaw('1 = 0');
            }
        }
        $orders = $ordersQuery->latest()->get();

        $response = response()->json([

            'guest_token' => Auth::check() ? null : $guestToken,

            'orders' =>
                $orders->map(function ($order) {

                    return [

                        'id' =>
                            $order->id,

                        'order_number' =>
                            $order->order_number,

                        'customer_name' =>
                            $order->customer_name,

                        'table_number' =>
                            $order->table_number,

                        'subtotal' =>
                            $order->subtotal,

                        'tax' =>
                            $order->tax,

                        'total' =>
                            $order->total,

                        'status' =>
                            $order->status,

                        'payment_status' =>
                            $order->payment_status,

                        'payment_method' =>
                            $order->payment
                                ? $order->payment->payment_method
                                : null,

                        'created_at' =>
                            $order->created_at
                                ? $order->created_at->format(
                                    'd M Y, H:i'
                                )
                                : '-',

                        'items' =>
                            $order->items
                                ->map(function ($item) {

                                    return [

                                        'id' => $item->id,

                                        'product_id' => $item->product_id,

                                        'product_name' =>
                                            $item->product_name,

                                        'review' => $item->review ? [
                                            'rating' => $item->review->rating,
                                            'comment' => $item->review->comment,
                                        ] : null,

                                        'customization' =>
                                            $item->customization,

                                        'quantity' =>
                                            $item->quantity,

                                        'price' =>
                                            $item->price,

                                        'subtotal' =>
                                            $item->subtotal,

                                    ];

                                })
                                ->values(),

                    ];

                })
                ->values(),
        ]);

        if (!Auth::check() && $guestToken && !request()->cookie('quattro_guest_token')) {
            $response->cookie('quattro_guest_token', $guestToken, 525600, '/', null, request()->isSecure(), true, false, 'lax');
        }

        return $response;
    }

    private function validGuestToken(?string $token): ?string
    {
        return is_string($token) && preg_match('/^[A-Za-z0-9]{64}$/', $token)
            ? $token
            : null;
    }

    private function configuredPrice(Product $product, array $customization): float
    {
        $category = strtolower(trim((string) $product->category));
        $isDrink = in_array($category, ['coffee', 'non coffee', 'non-coffee'], true);
        $hasOptions = collect($customization)
            ->except('note')
            ->filter(fn ($value) => !empty($value))
            ->isNotEmpty();

        if (!$isDrink && $hasOptions) {
            abort(422, 'Opsi racikan hanya tersedia untuk minuman.');
        }

        $price = (float) $product->price;
        return $price;
    }
}

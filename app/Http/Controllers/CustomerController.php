<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Favorite;
use App\Models\Payment;
use App\Models\PaymentAttempt;
use App\Models\ProductReview;
use App\Services\MidtransSnapService;
use App\Services\MidtransPaymentStatusService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Branch;

class CustomerController extends Controller
{
    private function customerProductsQuery()
    {
        $pendingReservations = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->whereColumn('order_items.product_id', 'products.id')
            ->where('payments.status', 'Pending')
            ->where('orders.payment_status', 'Belum Dibayar')
            ->where('orders.status', '!=', 'Dibatalkan')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0)');

        return Product::query()
            ->select('products.*')
            ->selectSub($pendingReservations, 'reserved_stock');
    }

    private function applyCustomerAvailableStock($products): void
    {
        foreach ($products as $product) {
            $availableStock = max(0, (int) $product->stock - (int) $product->reserved_stock);
            $product->stock = $availableStock;
            $product->is_available = (bool) $product->is_available && $availableStock > 0;
        }
    }

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
        $products = $this->customerProductsQuery()->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderBy('category')
            ->orderBy('name')
            ->get();
        $this->applyCustomerAvailableStock($products);
        $categories = config('product_categories');

        // Cabang aktif yang dipilih customer; default Bandung 1.
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $selectedBranch = Branch::whereKey(session('branch_id'))->where('is_active', true)->first()
            ?? Branch::where('code', 'BDG-001')->where('is_active', true)->first()
            ?? $branches->first();

        if ($selectedBranch) {
            session(['branch_id' => $selectedBranch->id]);
        }

        $favoriteIds = Auth::check()
            ? Favorite::where('user_id', Auth::id())->pluck('product_id')->map(fn ($id) => (int) $id)->all()
            : array_map('intval', session('guest_favorite_ids', []));

        return view(
            'customer.home',
            compact('products', 'favoriteIds', 'categories', 'branches', 'selectedBranch')
        );
    }


    /**
     * =========================================================
     * HALAMAN ORDERS CUSTOMER
     * =========================================================
     */
    public function orders(): Response
    {
        $ordersQuery = Order::with(['items.review', 'payment.attempts']);
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
        $products = $this->customerProductsQuery()->orderBy('category')->orderBy('name')->get();
        $this->applyCustomerAvailableStock($products);
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
        $products = $this->customerProductsQuery()->orderBy('category')->orderBy('name')->get();
        $this->applyCustomerAvailableStock($products);

        return view('customer.checkout', compact('products'));
    }

    /** Halaman informasi pemesan dan pembayaran QRIS. */
    public function paymentPage(): View
    {
        $products = $this->customerProductsQuery()->orderBy('category')->orderBy('name')->get();
        $this->applyCustomerAvailableStock($products);

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
        $request->merge([
            'customer_phone' => preg_replace('/[\s().-]+/', '', (string) $request->input('customer_phone', '')),
            'customer_email' => trim((string) $request->input('customer_email', '')),
        ]);
        $data = $request->validate([

            'customer_name' => [
                Auth::check() ? 'nullable' : 'required',
                'string',
                'max:255',
            ],

            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^(?:0|\+?62)8[0-9]{8,11}$/'],

            'customer_email' => ['required', 'email:rfc', 'max:255'],

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

        $requestedQuantities = [];
        foreach ($data['items'] as $item) {
            $productId = (int) $item['product_id'];
            $requestedQuantities[$productId] = ($requestedQuantities[$productId] ?? 0) + (int) $item['quantity'];
        }

        $mergedItems = [];
        foreach ($data['items'] as $item) {
            $customization = $item['customization'] ?? [];
            ksort($customization);
            $key = (int) $item['product_id'] . ':' . json_encode($customization);
            if (isset($mergedItems[$key])) {
                $mergedItems[$key]['quantity'] += (int) $item['quantity'];
            } else {
                $mergedItems[$key] = [
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                    'customization' => $customization,
                ];
            }
        }

        $order = DB::transaction(function () use ($data, $guestToken, $requestedQuantities, $mergedItems) {

            $subtotal = 0;
            $products = Product::whereIn('id', array_keys($requestedQuantities))
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            foreach ($requestedQuantities as $productId => $quantity) {
                $product = $products->get($productId);
                if (!$product) {
                    abort(422, 'Salah satu produk sudah tidak tersedia. Silakan perbarui keranjang.');
                }
                if (!$product->is_available || (int) $product->stock < $quantity) {
                    abort(422, "Stok {$product->name} tersisa {$product->stock}. Kurangi jumlah pesanan lalu coba lagi.");
                }
                $reserved = OrderItem::query()
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->join('payments', 'payments.order_id', '=', 'orders.id')
                    ->where('order_items.product_id', $productId)
                    ->where('payments.status', 'Pending')
                    ->where('orders.payment_status', 'Belum Dibayar')
                    ->where('orders.status', '!=', 'Dibatalkan')
                    ->sum('order_items.quantity');
                $available = max(0, (int) $product->stock - (int) $reserved);
                if ($available < $quantity) {
                    abort(422, "Stok {$product->name} yang masih tersedia hanya {$available}. Kurangi jumlah pesanan lalu coba lagi.");
                }
            }


            /**
             * =================================================
             * CEK PRODUK
             * =================================================
             *
             * Produk boleh tetap tampil di menu,
             * tetapi TIDAK boleh masuk order jika Habis.
             */
            foreach ($mergedItems as $item) {
                $product = $products->get((int) $item['product_id']);
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

                'branch_id' => session('branch_id'),

                'customer_name' =>
                    trim($data['customer_name'] ?? Auth::user()?->name ?? ''),

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
                $product = $products->get((int) $item['product_id']);

                if (!$product || !$product->is_available) {
                    abort(422, 'Produk sudah habis atau tidak tersedia. Silakan pilih produk lain.');
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

        $paymentUrl = null;
        $paymentDeadline = null;
        $midtrans = app(MidtransSnapService::class);
        if ($midtrans->isConfigured()) {
            try {
                $attempt = $midtrans->createSession($order->payment);
                $paymentUrl = $attempt->payment_url;
                $paymentDeadline = $attempt->expires_at?->toIso8601String();
            } catch (\Throwable $exception) {
                report($exception);
            }
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

            'payment_url' => $paymentUrl,

            'payment_deadline' => $paymentDeadline,

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
        $ordersQuery = Order::with(['items.review', 'payment.attempts']);
        $guestToken = null;
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
                    $payment = $order->payment;
                    $attempt = $payment?->attempts?->sortByDesc('id')->first();
                    $rawPaymentStatus = strtolower(trim((string) ($order->payment_status ?? '')));
                    $paymentStatus = ($rawPaymentStatus === 'dibayar' || $payment?->status === 'Berhasil')
                        ? 'Dibayar'
                        : 'Belum Dibayar';
                    if ($paymentStatus !== 'Dibayar' && ($attempt?->status === 'Gagal' || $rawPaymentStatus === 'gagal')) {
                        $paymentStatus = 'Gagal';
                    }
                    if ($paymentStatus !== 'Dibayar' && $attempt?->expires_at?->isPast()) {
                        $paymentStatus = 'Kedaluwarsa';
                    }
                    if ($order->status === 'Dibatalkan' && $paymentStatus !== 'Dibayar') {
                        $paymentStatus = 'Dibatalkan';
                    }

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

                        'cancellation_reason' => $order->cancellation_reason,

                        'cancelled_by' => $order->cancelled_by,

                        'cancelled_at' => $order->cancelled_at?->format('d M Y, H:i'),

                        'payment_status' => $paymentStatus,

                        'payment_deadline' => $attempt?->expires_at?->toIso8601String(),

                        'payment_url' => $paymentStatus === 'Belum Dibayar'
                            && $attempt?->expires_at?->isFuture() ? route('customer.orders.payment.open', $order) : null,

                        'can_retry_payment' => in_array($paymentStatus, ['Belum Dibayar', 'Kedaluwarsa', 'Gagal'], true)
                            && strtoupper((string) $payment?->payment_method) === 'QRIS'
                            && $order->status !== 'Dibatalkan',

                        'can_cancel_order' => $order->status === 'Menunggu'
                            && $paymentStatus !== 'Dibayar'
                            && strtoupper((string) $payment?->payment_method) === 'QRIS',

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

    public function retryPayment(Request $request, Order $order, MidtransSnapService $midtrans)
    {
        abort_unless($this->customerCanAccessOrder($request, $order), 404);
        if ($order->status === 'Dibatalkan' || $order->payment_status === 'Dibayar') {
            return response()->json(['message' => 'Pesanan ini tidak dapat dibayar lagi.'], 422);
        }
        $payment = $order->payment;
        if (!$payment || $payment->payment_method !== 'QRIS') {
            return response()->json(['message' => 'Pembayaran ulang hanya tersedia untuk QRIS.'], 422);
        }
        $latest = $payment->attempts()->latest('id')->first();
        if ($latest && $latest->status === 'Pending' && $latest->expires_at?->isFuture() && $latest->payment_url && $latest->qr_code_url) {
            return response()->json(['payment_url' => route('customer.orders.payment.open', $order), 'payment_deadline' => $latest->expires_at->toIso8601String()]);
        }
        try {
            $payment->update(['status' => 'Pending']);
            $attempt = $midtrans->createSession($payment);
            return response()->json(['payment_url' => $attempt->payment_url, 'payment_deadline' => $attempt->expires_at?->toIso8601String()]);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['message' => 'QRIS belum dapat dibuat. Coba lagi beberapa saat.'], 503);
        }
    }

    public function cancelOrder(Request $request, Order $order, MidtransSnapService $midtrans, MidtransPaymentStatusService $statusService)
    {
        abort_unless($this->customerCanAccessOrder($request, $order), 404);
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $order->loadMissing('payment.attempts');
        $payment = $order->payment;
        if ($order->status === 'Dibatalkan') {
            return response()->json(['message' => 'Pesanan ini sudah dibatalkan.'], 422);
        }
        if ($order->status !== 'Menunggu'
            || !$payment
            || strtoupper((string) $payment->payment_method) !== 'QRIS'
            || $order->payment_status === 'Dibayar'
            || $payment->status === 'Berhasil') {
            return response()->json(['message' => 'Pembatalan hanya tersedia untuk pesanan QRIS yang belum dibayar dan masih menunggu.'], 422);
        }

        $attempt = $payment->attempts->sortByDesc('id')->first();
        if ($attempt && filled($attempt->gateway_order_id) && ($attempt->status === 'Pending' || filled($attempt->qr_code_url))) {
            try {
                $gatewayStatus = $midtrans->transactionStatus($attempt);
                $gatewayState = strtolower((string) ($gatewayStatus['transaction_status'] ?? ''));
                if ($gatewayState === 'settlement' || ($gatewayState === 'capture' && strtolower((string) ($gatewayStatus['fraud_status'] ?? '')) === 'accept')) {
                    $statusService->apply($attempt, $gatewayStatus);
                    return response()->json(['message' => 'Pembayaran sudah berhasil. Pesanan tidak dapat dibatalkan dari riwayat.'], 422);
                }
                if ($gatewayState === 'pending') {
                    try {
                        $cancelResult = $midtrans->cancelTransaction($attempt);
                        $cancelState = strtolower((string) ($cancelResult['transaction_status'] ?? ''));
                        if ($cancelState === 'settlement' || ($cancelState === 'capture' && strtolower((string) ($cancelResult['fraud_status'] ?? '')) === 'accept')) {
                            $statusService->apply($attempt, $cancelResult);
                            return response()->json(['message' => 'Pembayaran sudah berhasil. Pesanan tidak dapat dibatalkan dari riwayat.'], 422);
                        }
                        if (!in_array($cancelState, ['cancel', 'expire', 'deny'], true)) {
                            $cancelResult = $midtrans->transactionStatus($attempt);
                            $cancelState = strtolower((string) ($cancelResult['transaction_status'] ?? ''));
                        }
                        if (!in_array($cancelState, ['cancel', 'expire', 'deny'], true)) {
                            throw new \RuntimeException('Status pembatalan QRIS belum final.');
                        }
                    } catch (\Throwable $cancelException) {
                        // The request may race with expiry or a successful payment.
                        try {
                            $gatewayStatus = $midtrans->expireTransaction($attempt);
                        } catch (\Throwable) {
                            $gatewayStatus = $midtrans->transactionStatus($attempt);
                        }
                        $gatewayState = strtolower((string) ($gatewayStatus['transaction_status'] ?? ''));
                        if ($gatewayState === 'settlement' || ($gatewayState === 'capture' && strtolower((string) ($gatewayStatus['fraud_status'] ?? '')) === 'accept')) {
                            $statusService->apply($attempt, $gatewayStatus);
                            return response()->json(['message' => 'Pembayaran sudah berhasil. Pesanan tidak dapat dibatalkan dari riwayat.'], 422);
                        }
                        if (!in_array($gatewayState, ['expire', 'cancel', 'deny'], true)) {
                            throw $cancelException;
                        }
                    }
                } elseif (!in_array($gatewayState, ['expire', 'cancel', 'deny'], true)) {
                    throw new \RuntimeException('Status pembayaran QRIS belum dapat dipastikan.');
                }
            } catch (\Throwable $exception) {
                report($exception);
                return response()->json(['message' => 'Status QRIS belum dapat dipastikan. Coba lagi sebentar.'], 503);
            }
        }

        try {
            DB::transaction(function () use ($order, $attempt, $validated) {
                $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
                $lockedPayment = Payment::where('order_id', $lockedOrder->id)->lockForUpdate()->first();
                if ($lockedOrder->status !== 'Menunggu'
                    || $lockedOrder->payment_status === 'Dibayar'
                    || $lockedPayment?->status === 'Berhasil') {
                    throw new \RuntimeException('Pesanan sudah dibayar atau statusnya berubah.');
                }
                if ($attempt) {
                    $lockedAttempt = PaymentAttempt::whereKey($attempt->id)->lockForUpdate()->first();
                    if ($lockedAttempt && $lockedAttempt->status === 'Pending') {
                        $lockedAttempt->update(['status' => 'Gagal']);
                    }
                }
                $lockedPayment?->update(['status' => 'Gagal']);
                $lockedOrder->update([
                    'status' => 'Dibatalkan',
                    'cancellation_reason' => trim((string) ($validated['reason'] ?? '')) ?: 'Dibatalkan oleh pelanggan.',
                    'cancelled_by' => 'customer',
                    'cancelled_at' => now(),
                ]);
            });
        } catch (\Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json(['message' => 'Pesanan berhasil dibatalkan. Stok reservasi sudah dilepas.']);
    }

    public function openPayment(Request $request, Order $order, MidtransSnapService $midtrans)
    {
        abort_unless($this->customerCanAccessOrder($request, $order), 404);
        if ($order->payment_status === 'Dibayar' || $order->payment?->status === 'Berhasil') {
            return redirect()->route('customer.history');
        }
        if ($order->status === 'Dibatalkan' || !$order->payment || $order->payment->payment_method !== 'QRIS') {
            return redirect()->route('customer.history')->with('payment_notice', 'Pesanan ini tidak dapat dibayar melalui QRIS.');
        }

        $payment = $order->payment;
        $attempt = $payment->attempts()->latest('id')->first();
        // Older QR attempts could carry an expiry timestamp parsed with a timezone
        // offset, leaving the UI open for hours. The Quattro QR deadline is always
        // 15 minutes from when this attempt was created.
        if ($attempt && $attempt->status === 'Pending' && $attempt->created_at) {
            $correctDeadline = $attempt->created_at->copy()->addMinutes(15);
            if (!$attempt->expires_at?->equalTo($correctDeadline)) {
                $attempt->update(['expires_at' => $correctDeadline]);
                $attempt->refresh();
            }
            if ($correctDeadline->isPast()) {
                $attempt->update(['status' => 'Gagal']);
            }
        }

        if ($attempt && $attempt->status === 'Pending' && $attempt->qr_code_url) {
            $storedQrUrl = parse_url($attempt->qr_code_url);
            $trustedQrHosts = ['api.midtrans.com', 'api.sandbox.midtrans.com'];
            $qrPath = $storedQrUrl['path'] ?? '';
            if (in_array($storedQrUrl['host'] ?? '', $trustedQrHosts, true)
                && preg_match('#^/v[24]/qris/[^/]+/qr-code$#', $qrPath)) {
                $qrBaseUrl = config('services.midtrans.production') ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
                $attempt->update(['qr_code_url' => $qrBaseUrl . $qrPath]);
                $attempt->refresh();
            }
        }
        if (!$attempt || $attempt->status !== 'Pending' || !$attempt->expires_at?->isFuture() || !$attempt->qr_code_url) {
            try {
                if ($attempt && $attempt->status === 'Pending') {
                    $attempt->update(['status' => 'Gagal']);
                }
                $payment->update(['status' => 'Pending']);
                $attempt = $midtrans->createSession($payment);
            } catch (\Throwable $exception) {
                report($exception);
                return redirect()->route('customer.history')->with('payment_notice', 'QRIS belum dapat dibuat. Periksa Server Key Sandbox Midtrans di .env, lalu coba lagi.');
            }
        }

        $qrImageData = null;
        $storedQrUrl = parse_url($attempt->qr_code_url);
        $qrPath = $storedQrUrl['path'] ?? '';
        if (in_array($storedQrUrl['host'] ?? '', ['api.midtrans.com', 'api.sandbox.midtrans.com'], true)
            && preg_match('#^/v[24]/qris/[^/]+/qr-code$#', $qrPath)) {
            $qrBaseUrl = config('services.midtrans.production')
                ? 'https://api.midtrans.com'
                : 'https://api.sandbox.midtrans.com';
            try {
                $qrResponse = Http::accept('image/png')
                    ->withBasicAuth(config('services.midtrans.server_key'), '')
                    ->timeout(10)
                    ->get($qrBaseUrl . $qrPath);
                if ($qrResponse->successful() && str_starts_with(strtolower((string) $qrResponse->header('Content-Type')), 'image/')) {
                    $qrImageData = 'data:' . $qrResponse->header('Content-Type', 'image/png') . ';base64,' . base64_encode($qrResponse->body());
                }
            } catch (\Throwable $exception) {
                report($exception);
            }
        }
        $sandboxQrUrl = null;
        if (!config('services.midtrans.production')
            && in_array($storedQrUrl['host'] ?? '', ['api.midtrans.com', 'api.sandbox.midtrans.com'], true)
            && preg_match('#^/v[24]/qris/[^/]+/qr-code$#', $qrPath)) {
            $sandboxQrUrl = 'https://api.sandbox.midtrans.com' . $qrPath;
        }

        return response()->view('customer.qris-payment', [
            'order' => $order->load('items'),
            'attempt' => $attempt,
            'qrImageData' => $qrImageData,
            'sandboxQrUrl' => $sandboxQrUrl,
            'qrImageUrl' => URL::temporarySignedRoute(
                'customer.orders.payment.qr',
                $attempt->expires_at,
                ['order' => $order, 'attempt' => $attempt->id],
                false,
            ),
            'qrDownloadUrl' => URL::temporarySignedRoute(
                'customer.orders.payment.qr',
                $attempt->expires_at,
                ['order' => $order, 'attempt' => $attempt->id, 'download' => 1],
                false,
            ),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function paymentQr(Request $request, Order $order)
    {
        $signedAccess = URL::hasValidSignature($request, false, ['retry']) && $request->filled('attempt');
        $attempts = $order->payment?->attempts();
        $attempt = $signedAccess
            ? $attempts?->whereKey($request->query('attempt'))->first()
            : $attempts?->latest('id')->first();
        $canAccess = ($signedAccess && $attempt) || $this->customerCanAccessOrder($request, $order);
        if (!$canAccess || !$attempt || $attempt->status !== 'Pending' || !$attempt->expires_at?->isFuture() || !$attempt->qr_code_url) {
            \Illuminate\Support\Facades\Log::warning('QR image route rejected.', [
                'order_id' => $order->id,
                'customer_access' => $canAccess,
                'attempt_status' => $attempt?->status,
                'deadline_future' => $attempt?->expires_at?->isFuture(),
                'has_qr_url' => (bool) $attempt?->qr_code_url,
            ]);
            abort(404);
        }

        $qrUrl = parse_url($attempt->qr_code_url);
        $trustedHosts = ['api.midtrans.com', 'api.sandbox.midtrans.com'];
        abort_unless(in_array($qrUrl['host'] ?? '', $trustedHosts, true), 404);
        $expectedHost = config('services.midtrans.production') ? 'api.midtrans.com' : 'api.sandbox.midtrans.com';
        $expectedBaseUrl = config('services.midtrans.production') ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
        $qrPath = $qrUrl['path'] ?? '';
        abort_unless(preg_match('#^/v[24]/qris/[^/]+/qr-code$#', $qrPath), 404);
        $qrImageUrl = $expectedBaseUrl . $qrPath;
        if (($qrUrl['host'] ?? '') !== $expectedHost) {
            $attempt->update(['qr_code_url' => $qrImageUrl]);
        }

        $response = Http::accept('image/png')
            ->withBasicAuth(config('services.midtrans.server_key'), '')
            ->timeout(15)
            ->get($qrImageUrl);
        if (!$response->successful()) {
            \Illuminate\Support\Facades\Log::warning('Midtrans QR image fetch failed.', [
                'order_id' => $order->id,
                'http_status' => $response->status(),
                'content_type' => $response->header('Content-Type'),
                'host' => $expectedHost,
            ]);
            abort(502, 'QR pembayaran belum dapat dimuat.');
        }

        $headers = ['Content-Type' => $response->header('Content-Type', 'image/png'), 'Cache-Control' => 'private, no-store'];
        if ($request->boolean('download')) {
            $headers['Content-Disposition'] = 'attachment; filename="quattro-' . preg_replace('/[^A-Za-z0-9_-]/', '', $order->order_number) . '-qris.png"';
        }
        return response($response->body(), 200, $headers);
    }

    public function checkPaymentStatus(Request $request, Order $order, MidtransSnapService $midtrans, MidtransPaymentStatusService $statusService)
    {
        abort_unless($this->customerCanAccessOrder($request, $order), 404);
        if ($order->payment_status === 'Dibayar' || $order->payment?->status === 'Berhasil') {
            return response()->json(['status' => 'Dibayar', 'message' => 'Pembayaran sudah berhasil.']);
        }
        $attempt = $order->payment?->attempts()->latest('id')->first();
        if (!$attempt || $attempt->status !== 'Pending') {
            return response()->json(['status' => 'Kedaluwarsa', 'message' => 'Sesi QRIS sudah berakhir. Silakan buat QR baru.']);
        }

        try {
            $status = $midtrans->transactionStatus($attempt);
            $result = $statusService->apply($attempt, $status);
            return response()->json([
                'status' => $result,
                'message' => $result === 'Dibayar' ? 'Pembayaran berhasil dikonfirmasi.' : ($result === 'Menunggu' ? 'Pembayaran belum terdeteksi. Jika sudah membayar, tunggu sebentar lalu cek kembali.' : 'QRIS kedaluwarsa. Silakan buat QR baru.'),
            ]);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['message' => 'Status belum dapat diperiksa. Coba kembali sebentar lagi.'], 503);
        }
    }

    public function paymentFinish(Order $order)
    {
        return redirect()->route('customer.history')->with('payment_notice', 'Terima kasih. Status pembayaran sedang diverifikasi otomatis.');
    }

    private function customerCanAccessOrder(Request $request, Order $order): bool
    {
        if (Auth::check()) {
            return (int) $order->user_id === (int) Auth::id();
        }
        $token = $this->validGuestToken($request->cookie('quattro_guest_token'))
            ?: $this->validGuestToken($request->header('X-Guest-Token'));
        return ($token && hash_equals((string) $order->guest_token, $token))
            || in_array((int) $order->id, array_map('intval', session('guest_order_ids', [])), true);
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

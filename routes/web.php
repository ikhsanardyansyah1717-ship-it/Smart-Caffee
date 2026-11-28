<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KasirBannerController;
use App\Http\Controllers\SiteBannerImageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProductAvailabilityController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\CustomerManagementController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeSalaryController;
use App\Http\Controllers\EmployeeLeaveController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OwnerSettingsController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\WhatsAppLoginWebhookController;

/* WELCOME */
Route::get('/', function (\App\Http\Controllers\CustomerController $customerController, \App\Services\SiteSettingsService $settings) {
    $user = auth()->user();
    if ($user && $user->role !== 'customer') {
        return view('welcome');
    }

    return $customerController->home($settings);
})->name('welcome');
Route::get('/product-images/{product}', [ProductImageController::class, 'show'])->name('products.image');
Route::get('/site-banners/{filename}', SiteBannerImageController::class)->where('filename', '[^/]+')->name('site-banners.image');
Route::get('/live/revisions', function () {
    $cache = Cache::store('file');

    return response()->json([
        'revisions' => collect(['products', 'orders', 'owner'])
            ->mapWithKeys(fn (string $type) => [$type => $cache->get('quattro.live.revision.' . $type)])
            ->all(),
    ])->header('Cache-Control', 'no-store, private');
})->name('live.revisions');

Route::post('/payments/midtrans/notification', MidtransWebhookController::class)
    ->name('payments.midtrans.notification');

/* CUSTOMER GUEST */
Route::prefix('customer')->name('customer.')->group(function () {
    Route::post('/branches/select', [BranchController::class, 'select'])->name('branches.select');
    Route::post('/branches/nearest', [BranchController::class, 'nearest'])->name('branches.nearest');
    Route::get('/tables/availability', [CustomerController::class, 'tableAvailability'])->name('tables.availability');
    Route::get('/home', [CustomerController::class, 'home'])->name('home');
    Route::get('/live/products', [CustomerController::class, 'liveProducts'])->name('live.products');
    Route::get('/history', [CustomerController::class, 'orders'])->name('history');
    Route::get('/orders', [CustomerController::class, 'checkoutPage'])->name('orders');
    Route::get('/payment', [CustomerController::class, 'paymentPage'])->name('payment');
    Route::get('/products/{product}/reviews', [CustomerController::class, 'productReviews'])->name('products.reviews');
    Route::post('/products/{product}/reviews', [CustomerController::class, 'storeProductReview'])->name('products.reviews.store');
    Route::post('/orders', [CustomerController::class, 'storeOrder'])->name('orders.store');
    Route::get('/orders/{order}/bank-transfer', [CustomerController::class, 'bankTransferInstructions'])->name('orders.bank-transfer');
    Route::post('/orders/{order}/bank-transfer/proof', [CustomerController::class, 'uploadBankTransferProof'])->name('orders.bank-transfer.proof');
    Route::get('/orders/{order}/bank-transfer/proof/image', [CustomerController::class, 'bankTransferProofImage'])->name('orders.bank-transfer.proof-image');
    Route::post('/orders/{order}/payment/retry', [CustomerController::class, 'retryPayment'])->name('orders.payment.retry');
    Route::post('/orders/{order}/cancel', [CustomerController::class, 'cancelOrder'])->name('orders.cancel');
    Route::get('/orders/{order}/payment', [CustomerController::class, 'openPayment'])->name('orders.payment.open');
    Route::get('/orders/{order}/payment/qr', [CustomerController::class, 'paymentQr'])->name('orders.payment.qr');
    Route::post('/orders/{order}/payment/check', [CustomerController::class, 'checkPaymentStatus'])->name('orders.payment.check');
    Route::post('/orders/{order}/payment/expire', [CustomerController::class, 'expireUnpaidOrder'])->name('orders.payment.expire');
    Route::get('/payment/finish/{order}', [CustomerController::class, 'paymentFinish'])->name('payment.finish');
    Route::get('/favorites', [CustomerController::class, 'favorites'])->name('favorites');
    Route::post('/favorites/toggle', [CustomerController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::get('/orders/history', [CustomerController::class, 'orderHistory'])->name('orders.history');
    Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
    Route::get('/privacy', [CustomerController::class, 'privacyPolicy'])->name('privacy');
});
Route::get('/customer/login', [AuthController::class, 'showLogin'])
    ->name('customer.login');

Route::get('/customer/login/google', [AuthController::class, 'redirectToGoogle'])
    ->middleware(['guest', 'throttle:20,1'])
    ->name('customer.login.google');

Route::get('/customer/login/google/callback', [AuthController::class, 'handleGoogleCallback'])
    ->middleware(['guest', 'throttle:20,1'])
    ->name('customer.login.google.callback');

Route::post('/customer/login/whatsapp/start', [AuthController::class, 'startWhatsAppLogin'])
    ->middleware(['guest', 'throttle:20,1'])
    ->name('customer.login.whatsapp.start');

Route::post('/customer/login/whatsapp/poll', [AuthController::class, 'pollWhatsAppLogin'])
    ->middleware(['guest', 'throttle:300,1'])
    ->name('customer.login.whatsapp.poll');

Route::get('/customer/login/whatsapp/return/{code}', [AuthController::class, 'returnFromWhatsApp'])
    ->middleware('signed')
    ->name('customer.login.whatsapp.return');

Route::get('/webhooks/whatsapp', [WhatsAppLoginWebhookController::class, 'verify'])
    ->name('webhooks.whatsapp.verify');
Route::post('/webhooks/whatsapp', [WhatsAppLoginWebhookController::class, 'receive'])
    ->name('webhooks.whatsapp.receive');

Route::middleware('guest')->group(function () {
    Route::get('/customer/register', [AuthController::class, 'showRegister'])
        ->name('customer.register');

    Route::post('/customer/register', [AuthController::class, 'register'])
        ->name('customer.register.store');
});

/* CUSTOMER AUTH */
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'redirectByRole'])
        ->name('role.dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

Route::middleware(['auth', 'role:customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/profile/edit', [CustomerController::class, 'editProfile'])
            ->name('profile.edit');
        Route::put('/profile', [CustomerController::class, 'updateProfile'])
            ->name('profile.update');
    });

/* ADMIN LOGIN */
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthAdminController::class, 'showLogin'])
        ->name('admin.login');

    Route::post('/admin/login', [AuthAdminController::class, 'login'])
        ->name('admin.login.process');
});

/* ADMIN LOGOUT */
Route::post('/admin/logout', [AuthAdminController::class, 'logout'])
    ->middleware('auth')
    ->name('admin.logout');

/* KITCHEN */
Route::middleware(['auth', 'role:kitchen'])
    ->prefix('kitchen')
    ->name('kitchen.')
    ->group(function () {
        Route::get('/dashboard', [KitchenController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/incoming', [KitchenController::class, 'incoming'])
            ->name('incoming');

        Route::get('/processing', [KitchenController::class, 'processing'])
            ->name('processing');

        Route::get('/completed', [KitchenController::class, 'completed'])
            ->name('completed');

        Route::get('/history', [KitchenController::class, 'history'])
            ->name('history');

        Route::get('/leave', [EmployeeLeaveController::class, 'employeeIndex'])->name('leave');
        Route::post('/leave', [EmployeeLeaveController::class, 'store'])->name('leave.store');

        Route::post('/orders/{id}/process', [KitchenController::class, 'process'])
            ->name('orders.process');

        Route::post('/orders/{id}/complete', [KitchenController::class, 'complete'])
            ->name('orders.complete');

        // Pertahankan nama route pickup lama.
        Route::post('/orders/{id}/pickup', [KitchenController::class, 'confirmPickup'])
            ->name('orders.pickup');

        // Route pickup alternatif, jika dipakai oleh tampilan baru.
        Route::post('/orders/{id}/confirm-pickup', [KitchenController::class, 'confirmPickup'])
            ->name('orders.confirmPickup');
    });

/* OWNER */
Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/notifications/read/{menu}', function (string $menu) {
            $destinations = [
                'finance' => 'expenses',
                'customers' => 'customers.index',
                'products' => 'products.index',
                'employees' => 'employees.index',
                'leaves' => 'leaves.index',
            ];
            abort_unless(isset($destinations[$menu]), 404);

            Cache::store('file')->forever('owner.notifications.seen.' . auth()->id() . '.' . $menu, now()->toDateTimeString());

            return redirect()->route('owner.' . $destinations[$menu]);
        })->name('menu-notifications.read');

        Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/sales', [OwnerController::class, 'sales'])
            ->name('sales');

        Route::get('/sales/export/excel', [OwnerController::class, 'exportSalesExcel'])
            ->name('sales.export.excel');

        Route::get('/sales/export/pdf', [OwnerController::class, 'exportSalesPdf'])
            ->name('sales.export.pdf');
        Route::resource('/products', ProductController::class);
        Route::get('/settings', [OwnerSettingsController::class, 'edit'])->name('settings');
        Route::put('/settings', [OwnerSettingsController::class, 'update'])->name('settings.update');
        Route::put('/settings/privacy-policy', [OwnerSettingsController::class, 'updatePrivacyPolicy'])->name('settings.privacy-policy.update');
        Route::get('/banners', [KasirBannerController::class, 'ownerEdit'])->name('banners.edit');
        Route::post('/banners', [KasirBannerController::class, 'ownerUpdate'])->name('banners.update');
        Route::resource('/employees', EmployeeController::class);
        Route::post('/employees/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.reset-password');
        Route::post('/employees/{employee}/change-password', [EmployeeController::class, 'changePassword'])->name('employees.change-password');
        Route::get('/leaves', [EmployeeLeaveController::class, 'ownerIndex'])->name('leaves.index');
        Route::patch('/leaves/{leaveRequest}', [EmployeeLeaveController::class, 'review'])->name('leaves.review');
        Route::get('/salaries', [EmployeeSalaryController::class, 'index'])->name('salaries.index');
        Route::put('/salaries/{employee}', [EmployeeSalaryController::class, 'update'])->name('salaries.update');
        Route::post('/salaries/payments', [EmployeeSalaryController::class, 'recordPayment'])->name('salaries.payments.store');
        Route::resource('/customers', CustomerManagementController::class);

        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports');

        Route::get('/expenses', [ExpenseController::class, 'index'])
            ->name('expenses');

        Route::post('/expenses', [ExpenseController::class, 'store'])
            ->name('expenses.store');

        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])
            ->name('expenses.destroy');

        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])
            ->name('reports.export.excel');

        Route::get('/reports/export/customers', [ReportController::class, 'exportCustomers'])
            ->name('reports.export.customers');

        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])
            ->name('reports.export.pdf');
    });

/* KASIR */
Route::middleware(['auth', 'role:kasir'])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () {
        Route::get('/dashboard', [KasirController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/banners', [KasirBannerController::class, 'edit'])->name('banners.edit');
        Route::post('/banners', [KasirBannerController::class, 'update'])->name('banners.update');

        Route::get('/tables/availability', [KasirController::class, 'tableAvailability'])->name('tables.availability');

        Route::get('/orders', [KasirController::class, 'orders'])
            ->name('orders');

        Route::post('/orders', [KasirController::class, 'storeOrder'])
            ->name('orders.store');

        Route::get('/orders/{id}/transfer-proof', [KasirController::class, 'transferProof'])->name('orders.transfer-proof');

        Route::post('/orders/{id}/confirm', [KasirController::class, 'confirmManualPayment'])
            ->name('orders.confirm');

        Route::post('/orders/{id}/qris/create', [KasirController::class, 'createCashierQris'])
            ->name('orders.qris.create');

        Route::post('/orders/{id}/payment/check', [KasirController::class, 'checkGatewayPayment'])
            ->name('orders.payment.check');

        Route::post('/orders/{id}/kitchen/confirm', [KasirController::class, 'releaseToKitchen'])
            ->name('orders.kitchen.confirm');

        Route::post('/orders/{id}/pickup', [KasirController::class, 'confirmPickup'])->name('orders.pickup');

        Route::post('/orders/{id}/finish-table', [KasirController::class, 'finishTable'])
            ->name('orders.finishTable');

        // Halaman pembayaran lama diarahkan ke daftar pesanan.
        Route::get('/payment', function () {
            return redirect()->route('kasir.orders');
        })->name('payment');

        Route::get('/history', [KasirController::class, 'history'])
            ->name('history');

        Route::get('/leave', [EmployeeLeaveController::class, 'employeeIndex'])->name('leave');
        Route::post('/leave', [EmployeeLeaveController::class, 'store'])->name('leave.store');

        // Pengelolaan ketersediaan produk dan stok.
        Route::get('/products/availability', [ProductAvailabilityController::class, 'index'])
            ->name('products.availability');

        Route::patch('/products/{product}/stock', [ProductAvailabilityController::class, 'updateStock'])
            ->name('products.stock.update');

    });






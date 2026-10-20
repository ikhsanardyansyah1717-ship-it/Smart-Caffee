
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductAvailabilityController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerManagementController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;

/* WELCOME */
Route::view('/', 'welcome')->name('welcome');

/* CUSTOMER GUEST */
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/home', [CustomerController::class, 'home'])->name('home');
    Route::get('/history', [CustomerController::class, 'orders'])->name('history');
    Route::get('/orders', [CustomerController::class, 'checkoutPage'])->name('orders');
    Route::get('/payment', [CustomerController::class, 'paymentPage'])->name('payment');
    Route::get('/products/{product}/reviews', [CustomerController::class, 'productReviews'])->name('products.reviews');
    Route::post('/products/{product}/reviews', [CustomerController::class, 'storeProductReview'])->name('products.reviews.store');
    Route::post('/orders', [CustomerController::class, 'storeOrder'])->name('orders.store');
    Route::get('/favorites', [CustomerController::class, 'favorites'])->name('favorites');
    Route::post('/favorites/toggle', [CustomerController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::get('/orders/history', [CustomerController::class, 'orderHistory'])->name('orders.history');
    Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
    Route::view('/privacy', 'customer.privacy')->name('privacy');
});
Route::get('/customer/login', [AuthController::class, 'showLogin'])
    ->name('customer.login');

Route::post('/customer/login', [AuthController::class, 'login'])
    ->name('customer.login.store');

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
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/sales', [OwnerController::class, 'sales'])
            ->name('sales');

        Route::get('/sales/export/excel', [OwnerController::class, 'exportSalesExcel'])
            ->name('sales.export.excel');

        Route::get('/sales/export/pdf', [OwnerController::class, 'exportSalesPdf'])
            ->name('sales.export.pdf');

        Route::resource('/products', ProductController::class);
        Route::resource('/employees', EmployeeController::class);
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

        Route::get('/orders', [KasirController::class, 'orders'])
            ->name('orders');

        Route::post('/orders', [KasirController::class, 'storeOrder'])
            ->name('orders.store');

        Route::post('/orders/{id}/confirm', [KasirController::class, 'confirmManualPayment'])
            ->name('orders.confirm');

        Route::post('/orders/{id}/finish-table', [KasirController::class, 'finishTable'])
            ->name('orders.finishTable');

        // Halaman pembayaran lama diarahkan ke daftar pesanan.
        Route::get('/payment', function () {
            return redirect()->route('kasir.orders');
        })->name('payment');

        Route::get('/history', [KasirController::class, 'history'])
            ->name('history');

        // Pengelolaan ketersediaan produk dan stok.
        Route::get('/products/availability', [ProductAvailabilityController::class, 'index'])
            ->name('products.availability');

        Route::post('/products', [ProductAvailabilityController::class, 'store'])
            ->name('products.store');

        Route::put('/products/{id}', [ProductAvailabilityController::class, 'update'])
            ->name('products.update');

        Route::delete('/products/{id}', [ProductAvailabilityController::class, 'destroy'])
            ->name('products.destroy');
    });

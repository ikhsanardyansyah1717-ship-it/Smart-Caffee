<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\CustomerController;


/*
|--------------------------------------------------------------------------
| WELCOME
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')->name('welcome');


/*
|--------------------------------------------------------------------------
| CUSTOMER GUEST
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // CUSTOMER LOGIN
    Route::get('/customer/login', [AuthController::class, 'showLogin'])
        ->name('customer.login');

    Route::post('/customer/login', [AuthController::class, 'login'])
        ->name('customer.login.store');

    // CUSTOMER REGISTER
    Route::get('/customer/register', [AuthController::class, 'showRegister'])
        ->name('customer.register');

    Route::post('/customer/register', [AuthController::class, 'register'])
        ->name('customer.register.store');

});

/*
|--------------------------------------------------------------------------
| CUSTOMER AUTH
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

});


/*
|--------------------------------------------------------------------------
| CUSTOMER
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {

        Route::get('/home', [CustomerController::class, 'home'])
            ->name('home');

        Route::get('/orders', [CustomerController::class, 'orders'])
            ->name('orders');

        Route::get('/favorites', [CustomerController::class, 'favorites'])
            ->name('favorites');

        Route::get('/profile', [CustomerController::class, 'profile'])
            ->name('profile');

    });


/*
|--------------------------------------------------------------------------
| ADMIN LOGIN
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/admin/login', [AuthAdminController::class, 'showLogin'])
        ->name('admin.login');

    Route::post('/admin/login', [AuthAdminController::class, 'login'])
        ->name('admin.login.process');
});


/*
|--------------------------------------------------------------------------
| ADMIN LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/admin/logout', [AuthAdminController::class, 'logout'])
    ->middleware('auth')
    ->name('admin.logout');


/*
|--------------------------------------------------------------------------
| KITCHEN
|--------------------------------------------------------------------------
*/

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
    });


/*
|--------------------------------------------------------------------------
| OWNER
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {

        Route::get('/dashboard', [OwnerController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/sales', [OwnerController::class, 'sales'])
            ->name('sales');

        Route::get('/products', [OwnerController::class, 'products'])
            ->name('products');

        Route::get('/employees', [OwnerController::class, 'employees'])
            ->name('employees');

        Route::get('/customers', [OwnerController::class, 'customers'])
            ->name('customers');

        Route::get('/reports', [OwnerController::class, 'reports'])
            ->name('reports');
    });


/*
|--------------------------------------------------------------------------
| KASIR
|--------------------------------------------------------------------------
*/

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

        Route::get('/payment', [KasirController::class, 'payment'])
            ->name('payment');

        Route::get('/history', [KasirController::class, 'history'])
            ->name('history');
    });

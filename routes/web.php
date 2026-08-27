<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\CustomerController;



Route::view('/', 'welcome')->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'redirectByRole'])->name('role.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'role:customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/home', [CustomerController::class, 'home'])->name('home');
        Route::get('/orders', [CustomerController::class, 'orders'])->name('orders');
        Route::get('/favorites', [CustomerController::class, 'favorites'])->name('favorites');
        Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
    });

Route::middleware(['auth', 'role:owner'])->get('/owner', function () {
    return 'Dashboard Owner belum dibuat.';
})->name('owner.dashboard');

Route::middleware(['auth', 'role:kitchen'])->get('/kitchen', function () {
    return 'Dashboard Kitchen belum dibuat.';
})->name('kitchen.dashboard');

Route::middleware(['auth', 'role:kasir'])->get('/kasir', function () {
    return 'Dashboard Kasir belum dibuat.';
})->name('kasir.dashboard');

/*
|--------------------------------------------------------------------------
| KITCHEN
|--------------------------------------------------------------------------
*/

Route::prefix('kitchen')->name('kitchen.')->group(function () {
    Route::get('/dashboard', [KitchenController::class, 'dashboard'])->name('dashboard');
    Route::get('/incoming', [KitchenController::class, 'incoming'])->name('incoming');
    Route::get('/processing', [KitchenController::class, 'processing'])->name('processing');
    Route::get('/completed', [KitchenController::class, 'completed'])->name('completed');
    Route::get('/history', [KitchenController::class, 'history'])->name('history');
});

/*
|--------------------------------------------------------------------------
| OWNER
|--------------------------------------------------------------------------
*/

Route::prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
    Route::get('/sales', [OwnerController::class, 'sales'])->name('sales');
    Route::get('/products', [OwnerController::class, 'products'])->name('products');
    Route::get('/employees', [OwnerController::class, 'employees'])->name('employees');
    Route::get('/customers', [OwnerController::class, 'customers'])->name('customers');
    Route::get('/reports', [OwnerController::class, 'reports'])->name('reports');
});

/*
|--------------------------------------------------------------------------
| KASIR
|--------------------------------------------------------------------------
*/

Route::prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [KasirController::class, 'orders'])->name('orders');
    Route::post('/orders', [KasirController::class, 'storeOrder'])->name('orders.store');
    Route::get('/payment', [KasirController::class, 'payment'])->name('payment');
    Route::get('/history', [KasirController::class, 'history'])->name('history');
});


<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrinkController;
use App\Http\Controllers\GuestController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/session/id/{market_session_id}', [GuestController::class, 'getMarketSession']);
Route::get('/session/id/{market_session_id}/all', [GuestController::class, 'getAllMarketSession']);
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/cashier', function () {
    return view('cashier');
})->middleware(['auth', 'verified'])->name('cashier');

Route::get('/qr-code', [GuestController::class, 'showQrCode'])->middleware(['auth', 'verified'])->name('qr-code');


Route::get('/about', [GuestController::class, 'showQrCode'])->name('about');

Route::get('/enter-code', [GuestController::class, 'enterCode'])->name('entercode');

Route::post('/code-submit', [GuestController::class, 'codeSubmit'])->name('codesubmit');

Route::get('/api/drink/{id}/price', [DrinkController::class, 'getMarketPrice']);
Route::get('/api/drink/{id}/price-history', [DrinkController::class, 'getMarketPriceHistory']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::post('/cashier/checkout', [DashboardController::class, 'checkout'])->name('checkout');
    Route::post('/market/create', [DashboardController::class, 'createMarket'])->name('market.create');

    
    Route::get('/drinks', [DrinkController::class, 'index'])->name('drinks');
    Route::get('/drinks/create', [DrinkController::class, 'create'])->name('drinks.create');
    Route::post('/drinks', [DrinkController::class, 'store'])->name('drinks.store');
    Route::get('/drinks/{drink}/edit', [DrinkController::class, 'edit'])->name('drinks.edit');
    Route::put('/drinks/{drink}', [DrinkController::class, 'update'])->name('drinks.update');

});


require __DIR__.'/auth.php';

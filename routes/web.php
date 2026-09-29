<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/realisations', [SiteController::class, 'portfolio'])->name('portfolio');
Route::get('/perruques', [ShopController::class, 'index'])->name('shop');
Route::get('/reserver', [BookingController::class, 'create'])->name('booking');

Route::middleware('guest')->group(function () {
    Route::view('/connexion', 'auth.login')->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::view('/inscription', 'auth.register')->name('register');
    Route::post('/inscription', [AuthController::class, 'register'])->middleware('throttle:6,1');
    Route::view('/mot-de-passe-oublie', 'auth.forgot')->name('password.request');
    Route::post('/mot-de-passe-oublie', [AuthController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/nouveau-mot-de-passe/{token}', fn (string $token) => view('auth.reset', ['token' => $token]))->name('password.reset');
    Route::post('/nouveau-mot-de-passe', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::get('/mon-compte', [AccountController::class, 'show'])->name('account');
    Route::get('/notifications', [AccountController::class, 'notifications'])->name('notifications');

    Route::post('/reserver', [BookingController::class, 'store'])->middleware('throttle:10,1');
    Route::post('/reservations/{booking}/annuler', [BookingController::class, 'cancel'])->name('booking.cancel');

    Route::post('/cabine/photo', [ShopController::class, 'uploadSelfie'])->name('selfie.store');
    Route::delete('/cabine/photo', [ShopController::class, 'deleteSelfie'])->name('selfie.delete');
    Route::get('/cabine/photo', [ShopController::class, 'selfie'])->name('selfie.show');
    Route::post('/perruques/{wig}/essayer', [ShopController::class, 'tryOn'])->name('wig.try');
    Route::post('/perruques/{wig}/commander', [ShopController::class, 'order'])->middleware('throttle:10,1')->name('wig.order');
    Route::get('/essais/{tryon}', [ShopController::class, 'tryonImage'])->name('tryon.image');
});

Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin')->controller(AdminController::class)->group(function () {
    Route::get('/', 'dashboard');
    Route::patch('/rdv/{booking}', 'bookingStatus')->name('.booking');
    Route::patch('/commandes/{order}', 'orderStatus')->name('.order');
    Route::get('/clientes', 'clients')->name('.clients');
    Route::get('/clientes/{user}', 'client')->name('.client');
    Route::get('/prestations', 'services')->name('.services');
    Route::post('/prestations', 'saveService')->name('.services.store');
    Route::put('/prestations/{service}', 'saveService')->name('.services.update');
    Route::delete('/prestations/{service}', 'deleteService')->name('.services.delete');
    Route::get('/perruques', 'wigs')->name('.wigs');
    Route::post('/perruques', 'storeWig')->name('.wigs.store');
    Route::put('/perruques/{wig}', 'updateWig')->name('.wigs.update');
    Route::delete('/perruques/{wig}', 'deleteWig')->name('.wigs.delete');
    Route::get('/realisations', 'portfolio')->name('.portfolio');
    Route::post('/realisations', 'storePortfolio')->name('.portfolio.store');
    Route::delete('/realisations/{item}', 'deletePortfolio')->name('.portfolio.delete');
});

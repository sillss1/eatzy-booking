<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantPhotoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home -> Redireciona para Login
Route::redirect('/', '/login');

// -------------------------------------
// Authentication (US01, US02, US17)
// -------------------------------------
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'register');
    Route::get('/logout', 'logout')->name('logout');
});
// -------------------------------------
// ADMIN PANEL (US47, US48, US49, US50)
// -------------------------------------
// Agora usamos 'auth' E 'is_admin'
Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])
        ->name('admin.dashboard');
    // User Management
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'listUsers'])
        ->name('admin.users');
    // Delete User Action
    Route::delete('/users/{id}', [App\Http\Controllers\AdminController::class, 'deleteUser'])
        ->name('admin.users.delete');
});

// -------------------------------------
// Static Pages (US08, US09)
// -------------------------------------
Route::get('/about', [StaticPageController::class, 'about'])->name('about');
Route::get('/faq', [StaticPageController::class, 'faq'])->name('faq');

// -------------------------------------
// Account Management (US18)
// -------------------------------------
Route::middleware('auth')->group(function () {
    // 1. Página para ver a conta e o botão de apagar (ESTA ESTAVA EM FALTA)
    Route::get('/account', function () {
        return view('auth.account');
    })->name('account');

    // 2. Ação de apagar a conta
    Route::delete('/user/delete', [UserController::class, 'deleteAccount'])->name('user.delete');
});

// -------------------------------------
// Reservations (US25, US26, US27, US28, US40)
// -------------------------------------
Route::middleware('auth')->controller(ReservationController::class)->group(function () {
    Route::get('/reservations', 'index')->name('reservations.index');
    Route::get('/restaurants/{restaurant_id}/reserve', 'create')->name('reservations.create');
    Route::post('/restaurants/{restaurant_id}/reserve', 'store')->name('reservations.store');
    Route::get('/reservations/{id}', 'show')->name('reservations.show');
    Route::delete('/reservations/{id}', 'destroy')->name('reservations.destroy');
    Route::get('/reservations/{id}/edit', 'edit')->name('reservations.edit');
    Route::put('/reservations/{id}', 'update')->name('reservations.update');
    Route::post('/reservations/{id}/cancel', 'cancel')->name('reservations.cancel');
    Route::post('reservations/{id}/confirm', 'confirm')->name('reservations.confirm');
});

// -------------------------------------
// Restaurants (Customer & Owner)
// -------------------------------------
Route::middleware('auth')->controller(RestaurantController::class)->group(function () {
    // Customer / Public
    Route::get('/restaurants', 'index')->name('restaurants.index');
    Route::get('/restaurants/{id}', 'show')->name('restaurants.show');

    // Owner (Create, Edit, Delete)
    Route::get('/owner/restaurants/create', 'create')->name('restaurants.create');
    Route::post('/owner/restaurants', 'store')->name('restaurants.store');
    Route::get('/owner/restaurants/{restaurant}/edit', 'edit')->name('restaurants.edit');
    Route::put('/owner/restaurants/{restaurant}', 'update')->name('restaurants.update');
    Route::delete('/owner/restaurants/{restaurant}', 'destroy')->name('restaurants.destroy');
});

// -------------------------------------
// Restaurant photos
// -------------------------------------
Route::middleware('auth')->controller(RestaurantPhotoController::class)->group(function () {
    Route::post('/owner/restaurants/{restaurant}/photos', 'store')->name('restaurants.photos.store');
    Route::put('/owner/restaurants/{restaurant}/photos/{photo}', 'update')->name('restaurants.photos.update');
    Route::delete('/owner/restaurants/{restaurant}/photos/{photo}', 'destroy')->name('restaurants.photos.destroy');
});

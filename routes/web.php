<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReservationController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;


// -------------------------------------
// Home
// -------------------------------------
Route::redirect('/', '/login');

// -------------------------------------
// Reservations
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
    });


// -------------------------------------
// Restaurants (US04 & US05)
// -------------------------------------
Route::middleware('auth')->controller(RestaurantController::class)->group(function () {

    // US04 – Browse restaurants
    Route::get('/restaurants', 'index')->name('restaurants.index');

    // US05 – View restaurant details
    Route::get('/restaurants/{id}', 'show')->name('restaurants.show');
});


// -------------------------------------
// Authentication
// -------------------------------------
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
});

Route::controller(LogoutController::class)->group(function () {
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

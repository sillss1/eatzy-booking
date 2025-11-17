<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ItemController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;


// -------------------------------------
// Home
// -------------------------------------
Route::redirect('/', '/login');


// -------------------------------------
// Cards
// -------------------------------------
Route::middleware('auth')->controller(CardController::class)->group(function () {
    Route::get('/cards', 'index')->name('cards.index');
    Route::get('/cards/{card}', 'show')->name('cards.show');
});


// -------------------------------------
// API 
// -------------------------------------
Route::middleware('auth')->controller(CardController::class)->group(function () {
    Route::post('/api/cards', 'store');
    Route::delete('/api/cards/{card}', 'destroy');
});

Route::middleware('auth')->controller(ItemController::class)->group(function () {
    Route::post('/api/cards/{card}/items', 'store');
    Route::patch('/api/items/{item}', 'update');
    Route::delete('/api/items/{item}', 'destroy');
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

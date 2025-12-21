<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantPhotoController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\TwoFactorController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/restaurants');

// -------------------------------------
// Authentication (US01, US02)
// -------------------------------------
Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'register');
});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// -------------------------------------
// Password Recovery
// -------------------------------------
// -------------------------------------
// Password Recovery
// -------------------------------------
Route::controller(PasswordResetController::class)->middleware('throttle:6,1')->group(function () {
    Route::get('/password/forgot', 'showLinkRequestForm')->name('password.forgot');
    Route::post('/password/email', 'sendResetLinkEmail')->name('password.email');
    Route::get('/password/reset/{token}', 'showResetForm')->name('password.reset');
    Route::post('/password/reset', 'reset')->name('password.update');
});

// -------------------------------------
// Two-Factor Authentication
// -------------------------------------
// 2FA verification (during login, no auth required)
Route::get('/2fa/verify', [TwoFactorController::class, 'showVerify'])->name('2fa.verify');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify.submit');

// 2FA management (requires auth)
Route::middleware('auth')->controller(TwoFactorController::class)->group(function () {
    Route::get('/2fa/setup', 'showSetup')->name('2fa.setup');
    Route::post('/2fa/enable', 'enable')->name('2fa.enable');
    Route::get('/2fa/disable', 'showDisable')->name('2fa.disable');
    Route::post('/2fa/disable', 'disable')->name('2fa.disable.submit');
});

// -------------------------------------
// Static Pages (US08, US09)
// -------------------------------------
Route::get('/about', [StaticPageController::class, 'about'])->name('about');
Route::get('/faq', [StaticPageController::class, 'faq'])->name('faq');

// -------------------------------------
// ADMIN PANEL (US47, US48, US49, US50)
// -------------------------------------
Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // User Management
    Route::get('/users', [AdminController::class, 'listUsers'])->name('admin.users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/users/{id}/block', [AdminController::class, 'blockUser'])->name('admin.users.block');
    Route::post('/users/{id}/unblock', [AdminController::class, 'unblockUser'])->name('admin.users.unblock');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');

    // Resource Management
    Route::get('/resources', [AdminController::class, 'listRestaurants'])->name('admin.resources');

    // Restaurant Management
    Route::get('/restaurants/create', [AdminController::class, 'createRestaurant'])->name('admin.restaurants.create');
    Route::post('/restaurants', [AdminController::class, 'storeRestaurant'])->name('admin.restaurants.store');
    Route::get('/restaurants/{id}/edit', [AdminController::class, 'editRestaurant'])->name('admin.restaurants.edit');
    Route::put('/restaurants/{id}', [AdminController::class, 'updateRestaurant'])->name('admin.restaurants.update');
    Route::delete('/restaurants/{id}', [AdminController::class, 'deleteRestaurant'])->name('admin.restaurants.delete');

    // Review Management
    Route::get('/reviews', [AdminController::class, 'listReviews'])->name('admin.reviews');
    Route::get('/reviews/{id}/edit', [AdminController::class, 'editReview'])->name('admin.reviews.edit');
    Route::put('/reviews/{id}', [AdminController::class, 'updateReview'])->name('admin.reviews.update');
    Route::delete('/reviews/{id}', [AdminController::class, 'deleteReview'])->name('admin.reviews.delete');
});

// -------------------------------------
// Account Management (US18)
// -------------------------------------
Route::middleware('auth')->controller(UserController::class)->group(function () {
    Route::get('/user', 'viewProfile')->name('account.me');
    Route::get('/user/edit', 'editProfile')->name('account.edit');
    Route::get('/user/{id}', 'viewProfile')->name('account.view');
    Route::post('/user/update', 'updateProfile')->name('account.update');
    Route::delete('/user/delete', 'deleteAccount')->name('account.delete');
    Route::delete('/user/remove-picture', 'removePicture')->name('account.remove_picture');
});

// -------------------------------------
// Restaurants (Public)
// -------------------------------------
Route::controller(RestaurantController::class)->group(function () {
    Route::get('/restaurants', 'index')->name('restaurants.index');
    Route::get('/restaurants/{id}', 'show')->name('restaurants.show');
});

// -------------------------------------
// Restaurants (Owner Management)
// -------------------------------------
Route::middleware('auth')->prefix('owner')->controller(RestaurantController::class)->group(function () {
    Route::get('/restaurants/create', 'create')->name('restaurants.create');
    Route::post('/restaurants', 'store')->name('restaurants.store');
    Route::get('/restaurants/{restaurant}/edit', 'edit')->name('restaurants.edit');
    Route::put('/restaurants/{restaurant}', 'update')->name('restaurants.update');
    Route::delete('/restaurants/{restaurant}', 'destroy')->name('restaurants.destroy');
});

// -------------------------------------
// Restaurant Photos
// -------------------------------------
Route::middleware('auth')->prefix('owner')->controller(RestaurantPhotoController::class)->group(function () {
    Route::post('/restaurants/{restaurant}/photos', 'store')->name('restaurants.photos.store');
    Route::get('/restaurants/{restaurant}/photos/edit', 'editPhotos')->name('restaurants.photos.edit');
    Route::put('/restaurants/{restaurant}/photos/{photo}', 'update')->name('restaurants.photos.update');
    Route::delete('/restaurants/{restaurant}/photos/{photo}', 'destroy')->name('restaurants.photos.destroy');
});

// -------------------------------------
// Favourites
// -------------------------------------
Route::middleware('auth')->controller(FavouriteController::class)->group(function () {
    Route::post('/restaurants/{id}/favourite', 'toggle')->name('restaurants.favourite.toggle');
});

// -------------------------------------
// Reservations (US25, US26, US27, US28, US40)
// -------------------------------------
Route::middleware('auth')->controller(ReservationController::class)->group(function () {
    Route::get('/reservations', 'index')->name('reservations.index');
    Route::get('/restaurants/{restaurant_id}/reserve', 'create')->name('reservations.create');
    Route::post('/restaurants/{restaurant_id}/reserve', 'store')->name('reservations.store');
    Route::get('/reservations/{id}', 'show')->name('reservations.show');
    Route::get('/reservations/{id}/edit', 'edit')->name('reservations.edit');
    Route::put('/reservations/{id}', 'update')->name('reservations.update');
    Route::delete('/reservations/{id}', 'destroy')->name('reservations.destroy');
    Route::post('/reservations/{id}/cancel', 'cancel')->name('reservations.cancel');
    Route::post('/reservations/{id}/confirm', 'confirm')->name('reservations.confirm');
});

// -------------------------------------
// Reviews (US29, US53, US54)
// -------------------------------------
Route::middleware('auth')->controller(ReviewController::class)->group(function () {
    Route::post('/restaurants/{restaurant}/reviews', 'store')->name('reviews.store');
    Route::get('/reviews/{review}/edit', 'edit')->name('reviews.edit');
    Route::put('/reviews/{review}', 'update')->name('reviews.update');
    Route::delete('/reviews/{review}', 'destroy')->name('reviews.destroy');
});

// -------------------------------------
// Replies
// -------------------------------------
Route::middleware('auth')->controller(ReplyController::class)->group(function () {
    Route::post('/reviews/{review}/reply', 'store')->name('replies.store');
    Route::get('/replies/{reply}/edit', 'edit')->name('replies.edit');
    Route::put('/replies/{reply}', 'update')->name('replies.update');
    Route::delete('/replies/{reply}', 'destroy')->name('replies.destroy');
});

// -------------------------------------
// Notifications
// -------------------------------------
Route::middleware('auth')->controller(NotificationController::class)->group(function () {
    Route::get('/notifications', 'index')->name('notifications.index');
    Route::post('/notifications/{id}/read', 'markRead')->name('notifications.read');
    Route::post('/notifications/read-all', 'markAllRead')->name('notifications.read_all');
});

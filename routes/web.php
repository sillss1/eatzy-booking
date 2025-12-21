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


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/restaurants');

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
// Password Recovery
// -------------------------------------
use App\Http\Controllers\PasswordResetController;

Route::controller(PasswordResetController::class)->group(function () {
    Route::get('/password/forgot', 'showForgotForm')->name('password.forgot');
    Route::post('/password/email', 'sendResetLink')->name('password.email');
    Route::get('/password/reset/{token}', 'showResetForm')->name('password.reset');
    Route::post('/password/reset', 'resetPassword')->name('password.update');
});

// -------------------------------------
// Two-Factor Authentication
// -------------------------------------
use App\Http\Controllers\TwoFactorController;

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
    // Block/Unblock User Actions
    Route::post('/users/{id}/block', [App\Http\Controllers\AdminController::class, 'blockUser'])
        ->name('admin.users.block');
    Route::post('/users/{id}/unblock', [App\Http\Controllers\AdminController::class, 'unblockUser'])
        ->name('admin.users.unblock');
    // Edit User
    Route::get('/users/{id}/edit', [App\Http\Controllers\AdminController::class, 'editUser'])
        ->name('admin.users.edit');
    Route::put('/users/{id}', [App\Http\Controllers\AdminController::class, 'updateUser'])
        ->name('admin.users.update');
    // Create User
    Route::get('/users/create', [App\Http\Controllers\AdminController::class, 'createUser'])
        ->name('admin.users.create');
    Route::post('/users', [App\Http\Controllers\AdminController::class, 'storeUser'])
        ->name('admin.users.store');

    // ===== RESOURCE MANAGEMENT =====
    Route::get('/resources', [App\Http\Controllers\AdminController::class, 'listRestaurants'])
        ->name('admin.resources');

    // Restaurant Management
    Route::get('/restaurants/create', [App\Http\Controllers\AdminController::class, 'createRestaurant'])
        ->name('admin.restaurants.create');
    Route::post('/restaurants', [App\Http\Controllers\AdminController::class, 'storeRestaurant'])
        ->name('admin.restaurants.store');
    Route::get('/restaurants/{id}/edit', [App\Http\Controllers\AdminController::class, 'editRestaurant'])
        ->name('admin.restaurants.edit');
    Route::put('/restaurants/{id}', [App\Http\Controllers\AdminController::class, 'updateRestaurant'])
        ->name('admin.restaurants.update');
    Route::delete('/restaurants/{id}', [App\Http\Controllers\AdminController::class, 'deleteRestaurant'])
        ->name('admin.restaurants.delete');

    // Review Management
    Route::get('/reviews', [App\Http\Controllers\AdminController::class, 'listReviews'])
        ->name('admin.reviews');
    Route::get('/reviews/{id}/edit', [App\Http\Controllers\AdminController::class, 'editReview'])
        ->name('admin.reviews.edit');
    Route::put('/reviews/{id}', [App\Http\Controllers\AdminController::class, 'updateReview'])
        ->name('admin.reviews.update');
    Route::delete('/reviews/{id}', [App\Http\Controllers\AdminController::class, 'deleteReview'])
        ->name('admin.reviews.delete');
});

// -------------------------------------
// Static Pages (US08, US09)
// -------------------------------------
Route::get('/about', [StaticPageController::class, 'about'])->name('about');
Route::get('/faq', [StaticPageController::class, 'faq'])->name('faq');

// -------------------------------------
// Account Management (US18)
// -------------------------------------
Route::middleware('auth')->controller(UserController::class)->group(function () {
    Route::get('/user', 'viewProfile')->name('account.me');
    Route::get('/user/edit', 'editProfile')->name('account.edit');
    Route::get('/user/{id}', 'viewProfile')->name('account.view');
    Route::post('/user/update', 'updateProfile')->name('account.update');
    Route::delete('/user/delete', 'deleteAccount')->name('user.delete');
    Route::delete('/user/remove-picture', 'removePicture')->name('account.remove_picture');
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
// Restaurants (Public)
// -------------------------------------
Route::controller(RestaurantController::class)->group(function () {
    Route::get('/restaurants', 'index')->name('restaurants.index');
    Route::get('/restaurants/{id}', 'show')->name('restaurants.show');
});

// -------------------------------------
// Restaurants (Customer & Owner)
// -------------------------------------
Route::middleware('auth')->controller(RestaurantController::class)->group(function () {
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
    Route::get('/owner/restaurants/{restaurant}/photos/edit', 'editPhotos')->name('restaurants.photos.edit');
    Route::put('/owner/restaurants/{restaurant}/photos/{photo}', 'update')->name('restaurants.photos.update');
    Route::delete('/owner/restaurants/{restaurant}/photos/{photo}', 'destroy')->name('restaurants.photos.destroy');
});


Route::middleware('auth')->controller(FavouriteController::class)->group(function () {
    Route::post('/restaurants/{id}/favourite', 'toggle')->name('restaurants.favourite.toggle');
});

// -------------------------------------
// Reviews (US29, US53, US54)
// -------------------------------------
Route::middleware('auth')->controller(ReviewController::class)->group(function () {
    // Create review for a restaurant
    Route::post('/restaurants/{restaurant}/reviews', 'store')->name('reviews.store');

    // Edit existing review
    Route::get('/reviews/{review}/edit', 'edit')->name('reviews.edit');

    // Update existing review
    Route::put('/reviews/{review}', 'update')->name('reviews.update');

    // Delete review
    Route::delete('/reviews/{review}', 'destroy')->name('reviews.destroy');
});

Route::middleware('auth')->controller(ReplyController::class)->group(function () {
    Route::post('/reviews/{review}/reply', 'store')->name('replies.store');
    Route::get('/replies/{reply}/edit', 'edit')->name('replies.edit');
    Route::put('/replies/{reply}', 'update')->name('replies.update');
    Route::delete('/replies/{reply}', 'destroy')->name('replies.destroy');
});

Route::middleware('auth')->controller(NotificationController::class)->group(function () {
    Route::get('/notifications', 'index')->name('notifications.index');
    Route::post('/notifications/{id}/read', 'markRead')->name('notifications.read');
    Route::post('/notifications/read-all', 'markAllRead')->name('notifications.read_all');
});

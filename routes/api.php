<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\UserController;

// Como o Sanctum falhou, usamos 'web' para aproveitar o login de sessão
Route::middleware('web')->group(function () {
    Route::get('/restaurants', [ApiController::class, 'getRestaurants']);
    Route::put('/reservations/{id}', [ApiController::class, 'updateReservation']);
    Route::delete('/reservations/{id}', [ApiController::class, 'deleteReservation']);
    Route::delete('/reviews/{id}', [ApiController::class, 'deleteReview']);
    Route::delete('/photos/{id}', [ApiController::class, 'deletePhoto']);
    Route::put('/admin/users/{id}/block', [UserController::class, 'blockUser']);
});

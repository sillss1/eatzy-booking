<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes use the session-based authentication from the web middleware
| since Sanctum is not configured.
|
*/

Route::middleware('web')->group(function () {
    Route::get('/restaurants', [ApiController::class, 'getRestaurants']);
    Route::put('/reservations/{id}', [ApiController::class, 'updateReservation']);
    Route::delete('/reservations/{id}', [ApiController::class, 'deleteReservation']);
    Route::delete('/reviews/{id}', [ApiController::class, 'deleteReview']);
    Route::delete('/photos/{id}', [ApiController::class, 'deletePhoto']);
});

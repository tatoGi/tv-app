<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TvAuthController;

// Auth routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('logout', [AuthController::class, 'logout']);
    // Add other protected routes here
});

// TV Authentication endpoints with new names
Route::post('generate-tv-code', [TvAuthController::class, 'generateTvCode'])
    ->middleware('throttle:6,1');

Route::post('active-tv-code', [TvAuthController::class, 'activateTvCode'])
    ->middleware(['auth:api', 'throttle:6,1'])
    ->middleware('auth:api')
    ->name('active-tv-code');

Route::post('poll-tv-code', [TvAuthController::class, 'pollTvCode'])
    ->middleware('throttle:30,1');
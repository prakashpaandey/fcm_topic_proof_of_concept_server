<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\InterestController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — consumed by the mobile application
|--------------------------------------------------------------------------
*/

// Public — no auth required
Route::post('/login', [AuthController::class, 'login']);

// Protected — require valid Sanctum token in Authorization: Bearer header
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Interests — global list
    Route::get('/interests', [InterestController::class, 'index']);

    // User interest selections
    Route::get('/user/interests',  [InterestController::class, 'userInterests']);
    Route::post('/user/interests', [InterestController::class, 'sync']);

    // Device token management
    Route::post('/device-tokens',   [DeviceTokenController::class, 'store']);
    Route::delete('/device-tokens', [DeviceTokenController::class, 'destroy']);

    // Post feed
    Route::get('/posts',     [PostController::class, 'index']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
});

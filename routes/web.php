<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InterestController;
use App\Http\Controllers\Admin\PostController;
use Illuminate\Support\Facades\Route;

// Redirect root to admin dashboard
Route::get('/', fn() => redirect()->route('admin.dashboard'));

// Admin Auth (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login',  [AuthController::class, 'showLogin'])->name('admin.login');
    Route::get('/login', fn() => redirect()->route('admin.login'))->name('login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
});

// Admin Panel (auth protected)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users CRUD
    Route::resource('users', UserController::class);

    // Interests CRUD
    Route::resource('interests', InterestController::class);

    // Posts CRUD
    Route::delete('posts/bulk', [PostController::class, 'bulkDestroy'])->name('posts.bulk-destroy');
    Route::resource('posts', PostController::class);

});

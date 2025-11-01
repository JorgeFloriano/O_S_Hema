<?php

use App\Http\Controllers\Api\Clients\OrderApiController;
use App\Http\Controllers\Api\Clients\UserApiController;
use App\Http\Controllers\Api\LoginController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [LoginController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [LoginController::class, 'logout'])->name('api.logout');
    Route::get('/auth/user', [LoginController::class, 'user']);
    Route::resource('/orders', OrderApiController::class);
    Route::resource('/users', UserApiController::class);
    // Your other protected API routes
});
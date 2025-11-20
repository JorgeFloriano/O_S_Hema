<?php
use App\Http\Controllers\Api\LoginController;
use Illuminate\Support\Facades\Route;

// Hema app client routes packages
use App\Http\Controllers\Api\Clients\OrderApiController;
use App\Http\Controllers\Api\Clients\UserApiController;

// Hema app team routes packages
use App\Http\Controllers\Api\Team\NoteTeamApiController;
use App\Http\Controllers\Api\Team\UserTeamApiController;

// Hema app client routes
Route::post('/auth/login', [LoginController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [LoginController::class, 'logout'])->name('api.logout');
    Route::get('/auth/user', [LoginController::class, 'user']);
    Route::resource('/orders', OrderApiController::class);
    Route::resource('/users', UserApiController::class);
    // Your other protected API routes
});

// Hema app team routes
Route::post('/auth/login_team', [LoginController::class, 'loginTeam']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [LoginController::class, 'logout'])->name('api.logout');
    Route::get('/auth/user', [LoginController::class, 'user']);
    Route::resource('/orders', NoteTeamApiController::class);
    Route::resource('/users', UserTeamApiController::class);
    // Your other protected API routes
});
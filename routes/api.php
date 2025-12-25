<?php
use App\Http\Controllers\Api\LoginController;
use Illuminate\Support\Facades\Route;

// Hema app client routes packages
use App\Http\Controllers\Api\Clients\OrderApiController;
use App\Http\Controllers\Api\Clients\UserApiController;
use App\Http\Controllers\Api\ExpoTokenController;
// Hema app team routes packages
use App\Http\Controllers\Api\Team\NoteTeamApiController;

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
    Route::get('/auth/user', [LoginController::class, 'user'])->name('auth.user');

    // Route::resource('/technician/orders', NoteTeamApiController::class);

    Route::get('/technician/orders', [NoteTeamApiController::class, 'index'])->name('tec-orders');
    Route::get('/notes/show/{id}', [NoteTeamApiController::class, 'show'])->name('notes.show');
    Route::get('/notes/create/{id}', [NoteTeamApiController::class, 'create'])->name('notes.create');
    Route::post('/notes', [NoteTeamApiController::class, 'store'])->name('notes.store');

    // Token push - versão simplificada
    Route::post('/expo-tokens/associate', [ExpoTokenController::class, 'associate']);

    Route::post('/technician/clear-emergency', [NoteTeamApiController::class, 'clearEmergency'])->name('tec-clear-emergency');
    // Route::resource('/users', UserTeamApiController::class);
    // Your other protected API routes
});
Route::post('/expo-tokens/register', [ExpoTokenController::class, 'register']);
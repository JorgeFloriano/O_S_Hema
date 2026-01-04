<?php

use App\Http\Controllers\Api\LoginController;
use Illuminate\Support\Facades\Route;

// Hema app client routes packages
use App\Http\Controllers\Api\Clients\OrderApiController;
use App\Http\Controllers\Api\Clients\UserApiController;
use App\Http\Controllers\Api\ExpoTokenController;
// Hema app team routes packages
use App\Http\Controllers\Api\Team\NoteTeamApiController;
use App\Http\Controllers\Api\Team\SatTeamApiController;
use Illuminate\Support\Facades\Auth;

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

    // Routes for tecnicians (notes)
    Route::get('/technician/orders', [NoteTeamApiController::class, 'index'])->name('tec-orders');
    Route::get('/notes/show/{id}', [NoteTeamApiController::class, 'show'])->name('notes.show');
    Route::get('/notes/create/{id}', [NoteTeamApiController::class, 'create'])->name('notes.create');
    Route::post('/notes', [NoteTeamApiController::class, 'store'])->name('notes.store');

    // Routes for SATs / orders
    Route::get('/sat/orders', [SatTeamApiController::class, 'index'])->name('sat-orders');

    // Token push - versão simplificada
    Route::post('/expo-tokens/associate', [ExpoTokenController::class, 'associate']);

    Route::post('/technician/clear-emergency', [NoteTeamApiController::class, 'clearEmergency'])->name('tec-clear-emergency');

    // Verificação se o técnico tem ordem de emergência sendo enviada no momento de abrir o aplicativo para ir direto para a tela da ordem
    Route::get('/technician/check-emergency', function () {
        $tec = Auth::user()->tec;
        return response()->json([
            'emergency_order_id' => $tec->emergency_order_id,
            'emergency_notification_pending' => (bool)$tec->emergency_notification_pending,
        ]);
    });

    // Route::resource('/users', UserTeamApiController::class);
    // Your other protected API routes
});
Route::post('/expo-tokens/register', [ExpoTokenController::class, 'register']);

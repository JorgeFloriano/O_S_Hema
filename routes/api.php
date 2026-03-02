<?php

use App\Http\Controllers\Api\LoginController;
use Illuminate\Support\Facades\Route;
use App\Models\Tec;
use Illuminate\Support\Facades\Auth;

// Hema app client routes packages
use App\Http\Controllers\Api\Clients\OrderApiController;
use App\Http\Controllers\Api\Clients\UserApiController;
use App\Http\Controllers\Api\ExpoTokenController;

// Hema app team routes packages
use App\Http\Controllers\Api\Team\NoteTeamApiController;
use App\Http\Controllers\Api\Team\SatTeamApiController;
use App\Http\Controllers\Api\Team\EmergencyApiController;
use Illuminate\Http\Request;

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
    Route::get('/sat/orders', [SatTeamApiController::class, 'apiIndex'])->name('sat-orders');

    // Routes for SATs / orders
    Route::post('/sat/orders/search', [SatTeamApiController::class, 'search'])->name('sat-search');

    // Token push - versão simplificada
    Route::post('/expo-tokens/associate', [ExpoTokenController::class, 'associate']);

    Route::post('/technician/clear-emergency', [NoteTeamApiController::class, 'clearEmergency'])->name('tec-clear-emergency');

    // Verificação se o técnico tem SAT de emergência sendo enviada no momento de abrir o aplicativo para ir direto para a tela da SAT
    Route::get('/technician/check-emergency', function () {
        $tec = Auth::user()->tec;
        return response()->json([
            'emergency_order_id' => $tec->emergency_order_id,
            'emergency_notification_pending' => (bool)$tec->emergency_notification_pending,
        ]);
    });

    // Enviar a lista de técnicos para o aplicativo
    Route::get('/tecs/list', function () {
        return Tec::join('users', 'tecs.user_id', '=', 'users.id')
            ->orderBy('users.name', 'asc')
            ->select('tecs.*')
            ->with('user:id,name,surname')
            ->get();
    });

    // Atualizar o tecnico da SAT
    Route::post('/sat/orders/{id}/update-tec', [SatTeamApiController::class, 'update_tec'])->name('sat-update-tec');

    // Reabrir SAT
    Route::put('/sat/orders/{id}/reopen', [SatTeamApiController::class, 'reopen'])->name('sat-reopen');

    // Deletar SAT
    Route::delete('/sat/orders/{id}/delete', [SatTeamApiController::class, 'destroy'])->name('sat-delete');

    // Download SAT
    Route::get('/sat/orders/{id}/download', [SatTeamApiController::class, 'download_pdf'])->name('sat-download-pdf');

    // Rotas para a tela de controle de técnicos de sobreaviso (emergência)
    Route::prefix('emergency')->group(function () {
        // Listagem de técnicos e clientes
        Route::get('/tecs', [EmergencyApiController::class, 'index']);
        Route::get('/clients', [EmergencyApiController::class, 'getClients']);

        // Atualizações
        Route::put('/tecs/{id}/toggle', [EmergencyApiController::class, 'toggleActive']);
        Route::put('/tecs/{id}/clients', [EmergencyApiController::class, 'syncClients']);
    });

    Route::get('/user-permissions', function (Request $request) {
        // Pegamos os nomes (pode vir como array names[]=perm1&names[]=perm2 ou string separada por vírgula)
        $names = $request->query('names');
        $level = (int) $request->query('level', 1);

        if (!$names) {
            return response()->json(['error' => 'Nomes não fornecidos'], 400);
        }

        // Se vier como string separada por vírgula, transformamos em array
        if (is_string($names)) {
            $names = explode(',', $names);
        }

        $results = [];
        $user = $request->user();

        foreach ($names as $name) {
            $results[$name] = $user->hasPermission($name, $level);
        }

        return response()->json($results);
    });
});

Route::post('/expo-tokens/register', [ExpoTokenController::class, 'register']);

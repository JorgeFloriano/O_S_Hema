<?php
// app/Http/Controllers/ExpoTokenController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpoToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExpoTokenController extends Controller
{
    /**
     * Registra ou atualiza token
     * Chame esta rota apenas quando token mudar (reinstalação do app)
     */
    public function register(Request $request)
    {
        logger_main('info', 'Teste info');
        return response()->json([
            'message' => 'Teste message',
            'errors' => 'Teste error',
        ]);

        $validator = Validator::make($request->all(), [
            'expo_push_token' => 'required|string|min:20',
        ]);

        if ($validator->fails()) {
            logger()->error($validator->errors());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (Auth::check()) {
            $user = Auth::user();
        }

        // Novo token
        $tokenValue = $request->expo_push_token;

        // Buscar token existente (em todo o sistema)
        $expoToken = ExpoToken::where('value', $tokenValue)->first();

        if ($expoToken) {
            // Token já existe, atualizar user_id se for diferente
            if ($user && $expoToken->user_id != $user->id) {
                logger_main('info', 'Token atualizado');
                $expoToken->update(['user_id' => $user->id]);
                $message = 'Token atualizado';
            } else {
                logger_main('info', 'Token ja existente');
                $message = 'Token já existe';
            }
        } else {
            // Novo token
            $expoToken = ExpoToken::create([
                'user_id' => $user ? $user->id : null,
                'value' => $tokenValue,
            ]);

            logger_main('info', 'Token registrado');
            $message = 'Token registrado';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'token' => $expoToken
        ]);
    }

    /**
     * Associa token existente ao usuário (após login)
     */
    public function associate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expo_push_token' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user) {
            logger_main('error', 'Usuário nao autenticado');
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        $token = ExpoToken::where('value', $request->expo_push_token)->first();

        if (!$token) {
            // Se token não existe, criar com user_id
            $token = ExpoToken::create([
                'user_id' => $user->id,
                'value' => $request->expo_push_token,
            ]);
            logger_main('info', 'Token criado e associado');

            $message = 'Token criado e associado';
        } else {
            // Atribui 0 ao user_id, antes de atualizar só para atualizar o updated_at e pegar o token mais recente na hora de enviar notificação
            $token->update(['user_id' => 0]);

            // Token existe, atualizar user_id
            $token->update(['user_id' => $user->id]);
            $message = 'Token associado ao usuário';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}

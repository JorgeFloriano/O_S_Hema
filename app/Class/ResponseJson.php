<?php

namespace App\Class;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ResponseJson
{
    public function error($conditions = [], $message)
    {
        // Verify if user has access the feature
        foreach ($conditions as $key => $condition) {
            if (!isset($condition)) {
                return response()->json([
                    'error' => $message,
                    'message' => $message
                ], 200);
            }

            if (!$condition) {
                return response()->json([
                    'error' => $message,
                    'message' => $message
                ], 200);
            }
        }

        return false;
    }

    public function numberOfUsersForClientLimit($auth)
    {
        // Return error if there is more than 3 users with the same cli->client_id
        $client_users_count = User::whereHas('cli', function ($query) use ($auth) {
            $query->where('client_id', $auth->cli->client_id);
        })->count();

        $message = 'Você já cadastrou ' . $client_users_count - 1 . ' usuários, limite atingido!';

        if ($client_users_count > 3) {
            return response()->json([
                'success' => false,
                'error' => $message,
                'message' => $message
            ], 404);
        }

        return false;
    }
    public function AuthIsTec()
    {
        $auth = Auth::user();
        if (!$auth->tec) {
            return response()->json([
                'success' => false,
                'error' => 'Usuário sem cadastro de técnico.',
                'message' => 'Usuário sem cadastro de técnico.'
            ], 404);
        }
    }

    public function AuthIsSup()
    {
        $auth = Auth::user();
        if (!$auth->sup) {
            return response()->json([
                'success' => false,
                'error' => 'Usuário sem cadastro de supervisor.',
                'message' => 'Usuário sem cadastro de supervisor.'
            ], 404);
        }
    }

    public function isAuth()
    {
        $auth = Auth::user();
        if (!$auth) {
            return response()->json([
                'success' => false,
                'error' => 'Usuário sem cadastro.',
                'message' => 'Usuário sem cadastro.'
            ], 404);
        }
    }
}

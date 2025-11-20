<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by username or email
        $user = User::where('username', $request->username)
                    ->orWhere('email', $request->username)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        if (!$user->cli) {
            throw ValidationException::withMessages([
                'username' => ['Esta versão do app requer uma conta de cliente.'],
            ]);
        }

        // Create token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'isClient' => $user->cli ? true : false,
                'clientId' => $user->cli ? $user->cli->client_id : null,
                'isAdmin' => $user->cli ? $user->cli->is_admin : null,
                'canCreateSat' => $user->cli ? $user->cli->can_create_sat : null,
                'canSeeSat' => $user->cli ? $user->cli->can_see_sat : null
            ]
        ]);
    }

    public function loginTeam(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:5',
        ]);

        // Find user by username
        $user = User::where('username', $request->username)
                    //->orWhere('email', $request->username)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // Create token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email ?? null,
                //'isClient' => $user->cli ? true : false,
                //'clientId' => $user->cli ? $user->cli->client_id : null,
                //'isAdmin' => $user->cli ? $user->cli->is_admin : null,
                //'canCreateSat' => $user->cli ? $user->cli->can_create_sat : null,
                //'canSeeSat' => $user->cli ? $user->cli->can_see_sat : null
            ]
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }
}
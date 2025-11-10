<?php

namespace App\Http\Controllers\Api\Clients;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormApiUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UserApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $auth = Auth::user();

            // Check if user has cli relationship and get client_id
            if (!$auth->cli) {
                return response()->json([
                    'error' => 'Usuário client não encontrado',
                    'users' => []
                ], 200);
            }

            $client_id = $auth->cli->client_id;

            // Fixed query - using whereHas for relationship filtering
            $users = User::with(['cli:id,client_id'])
                ->whereHas('cli', function ($query) use ($client_id) {
                    $query->where('client_id', $client_id)
                        ->where('is_admin', null);
                })
                ->get(['id', 'name', 'surname', 'function', 'username']);

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load users: ' . $e->getMessage(),
                'users' => []
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $auth = Auth::user();

        // Check if user has cli relationship and get client_id
        if (!$auth->cli) {
            return response()->json([
                'error' => 'Usuário client não encontrado',
            ], 200);
        }

        // Verify if user has access to create users
        if (!isset($auth->cli->is_admin)) {
            return response()->json([
                'error' => 'Usuário sem permissão para criar usuários.',
            ], 200);
        }

        if (!$auth->cli->is_admin) {
            return response()->json([
                'error' => 'Usuário sem permissão para criar usuários.',
            ], 200);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormApiUserRequest $request)
    {
        $auth = Auth::user();

        // Check if user has cli relationship and get client_id
        if (!$auth->cli) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário client não encontrado',
            ], 200);
        }

        // Verify if user has access to create users
        if (!$auth->cli->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário sem permissão para criar usuários.',
            ], 200);
        }

        try {

            // Create new user
            $user = User::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'email' => $request->email,
                'username' => $request->username,
                'function' => $request->function,
                'password' => Hash::make($request->password),
            ]);

            if ($user) {
                $client_id = $auth->cli->client_id;

                $user_cli = $user->cli()->create([
                    'user_id' => $user->id,
                    'client_id' => $client_id,
                    'is_admin' => null,
                    'can_create_sat' => $request->can_create_sat,
                    'can_see_sat' => $request->can_see_sat,
                ]);

                if (!$user_cli) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao liberar acesso de cliente para o usuário.'
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar cadastro de usuário.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cadastro de usuário criado com sucesso.',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar cadastro de usuário.' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function edit($id)
    {
        $auth = Auth::user();

        // Check if user has cli relationship and get client_id
        if (!$auth->cli) {
            return response()->json([
                'success' => false,
                'error' => 'Usuário client não encontrado',
            ], 404);
        }

        // Verify if user has access to edit users
        if (!$auth->cli->is_admin && $auth->id != $id) {
            return response()->json([
                'success' => false,
                'error' => 'Usuário sem permissão para editar outros usuários .'
            ], 404);
        }

        try {
            $user = User::findOrFail($id);
            // $user = User::with(['cli:id,client_id,can_create_sat,can_see_sat'])
            //     ->findOrFail($id);

            // Flatten the response for easier frontend consumption
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'username' => $user->username,
                'function' => $user->function,
                'can_create_sat' => $user->cli->can_create_sat ?? false,
                'can_see_sat' => $user->cli->can_see_sat ?? false,
                'client_id' => $user->cli->client_id ?? null,
            ];

            return response()->json([
                'success' => true,
                'user' => $userData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Usuário não encontrado',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FormApiUserRequest $request, $id)
    {
        $auth = Auth::user();

        // Check if user has cli relationship and get client_id
        if (!$auth->cli) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário client não encontrado',
            ], 200);
        }

        // Verify if user has access to edit users
        if (!$auth->cli->is_admin && $auth->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário sem permissão para editar outros usuários.',
            ], 200);
        }
        try {

            $user = User::findOrFail($id);

            $validated = $request->validated();

            // Update user data
            $user->update([
                'name' => $validated['name'],
                'surname' => $validated['surname'] ?? $user->surname,
                'email' => $validated['email'],
                'username' => $validated['username'],
                'function' => $validated['function'] ?? $user->function,
            ]);

            // Update cli data (user cant updated yourself permissions)
            if ($auth->id != $id && $auth->cli->is_admin) {
                $user->cli()->update([
                    'can_create_sat' => $validated['can_create_sat'] ?? false,
                    'can_see_sat' => $validated['can_see_sat'] ?? false,
                ]);
            }


            // Update password only if provided
            if ($request->password) {
                $user->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso!',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Falha ao atualizar usuário',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);

            // Check if user exists
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não encontrado.'
                ], 404);
            }

            // Prevent users from deleting their own account
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode excluir sua própria conta.'
                ], 422);
            }

            // Verify if user has access to delete users
            if (!Auth::user()->cli->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voce não tem permissão para excluir usuários.'
                ], 422);
            }

            // Delete the user and all of his accesses
            $user->CompletelyDelete();

            return response()->json([
                'success' => true,
                'message' => 'Usuário excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage(), [
                'user_id' => $id,
                'auth_user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Falha ao excluir usuário',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

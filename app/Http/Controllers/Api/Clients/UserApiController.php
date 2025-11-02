<?php

namespace App\Http\Controllers\Api\Clients;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormApiUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
                    'error' => 'Useuário client não encontrado',
                    'users' => []
                ], 200);
            }

            $client_id = $auth->cli->client_id;

            // Fixed query - using whereHas for relationship filtering
            $users = User::with(['cli:id,client_id'])
                ->whereHas('cli', function ($query) use ($client_id) {
                    $query->where('client_id', $client_id)
                    ->where('role', 'default');
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormApiUserRequest $request)
    {
        
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
                $auth = Auth::user();
                $client_id = $auth->cli->client_id;

                $user_cli = $user->cli()->create([
                    'user_id' => $user->id,
                    'client_id' => $client_id,
                    'role' => 'default'
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
                'message' => 'Erro ao criar cadastro de usuário.'.$e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

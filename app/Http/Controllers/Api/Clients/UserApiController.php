<?php

namespace App\Http\Controllers\Api\Clients;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                    'error' => 'User client relationship not found',
                    'users' => []
                ], 200);
            }

            $client_id = $auth->cli->client_id;

            // Fixed query - using whereHas for relationship filtering
            $users = User::with(['cli:id,client_id'])
                ->whereHas('cli', function ($query) use ($client_id) {
                    $query->where('client_id', $client_id);
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
    public function store(Request $request)
    {
        //
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

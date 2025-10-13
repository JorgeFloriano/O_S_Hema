<?php

namespace App\Http\Controllers\Api\Clients;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\OrderType;
use App\Models\Tec;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['message' => 'CORS is working new test!']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Create session variable wich contains all order types ids to validated in FormOrderRequest
        $types = OrderType::select('id', 'description')->get();
        session()->put('types_ids', $types->pluck('id')->toArray());
        
        return response()->json([
            'types' => $types
        ]);
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

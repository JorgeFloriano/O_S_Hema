<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function tec_on()
    {
        $tecs = Category::find(3)->users()->where('user_id', '!=', 1)->orderBy('user_id')->get();

        foreach ($tecs as $tec) {
            $tec_on = Category::find(2)->users()->where('user_id', $tec->id)->first();
            
            if (isset($tec_on)) {
                $tec->check = 'checked';
            } else {
                $tec->check = '';
            }
            
        }

        return view('tec_on', [
            'tecs' => $tecs
        ]);
    }

    public function tec_on_update(Request $request)
    {
        return $request->except(['_token', '_method']);
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

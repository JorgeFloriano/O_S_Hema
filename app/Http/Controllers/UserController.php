<?php

namespace App\Http\Controllers;

use App\Models\Tec;
use App\Models\User;
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
        $tecs = Tec::all();

        session()->put('tecs', $tecs);

        return view('tec_on', [
            'tecs' => $tecs
        ]);
    }

    public function tec_on_update(Request $request)
    {
        $tecs = session('tecs');

        foreach ($tecs as  $tec) {

            if ($request->input('tec'.$tec->id)) {
                $tec->on_call = 1;
                $up_on = $tec->save();
                if (!$up_on) {
                    return redirect()->back()->with('message', 'Erro ao atualizar registros.'); 
                }
            } else {
                $tec->on_call = 0;
                $up_off = $tec->save();
                if (!$up_off) {
                    return redirect()->back()->with('message', 'Erro ao atualizar registros.'); 
                }  
            }
        }

        return redirect()->back()->with('message', 'Registros atualizados com sucesso.');
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

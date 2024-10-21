<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormLoginRequest;
use App\Models\Adm;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function add() {

        // echo 'user saved';

        // $c_u2 = new Adm();
        // $c_u2->user_id = 2;
        // $c_u2->main = 0;
        // $c_u2->save();

        // $c_u2 = new Sup();
        // $c_u2->user_id = 1;
        // $c_u2->save();

        // $c_u2 = new NoteTec();
        // $c_u2->note_id = 2;
        // $c_u2->tec_id = 3;
        // $c_u2->save();

    }

    public function index()
    {
         // Check if the user is logged out
         if(auth()->user()) {

            $adm = auth()->user()->adm()->first();
            
            if ($adm) {
                if ($adm->main) {
                    return redirect()->route('clients.index');
                }
                return redirect()->route('orders.index');
            }

            $tec = auth()->user()->tec()->first();

            if ($tec) {
                return redirect()->route('notes.index');
            }

            $sup = auth()->user()->sup()->first();

            if ($sup) {
                return redirect()->route('tec_on');
            }
        }
        return view('login');
    }

    public function store(FormLoginRequest $request)
    {
        $request->validated();

        $credentials = $request->only('username', 'password');
        $authenticated = Auth::attempt($credentials);

        if (!$authenticated) {
            return redirect()->route('login.index')->withErrors(['error' => 'Credenciais inválidas']);
        }
        
        $adm = auth()->user()->adm()->first();

        if ($adm) {
            if ($adm->main) {
                
                session()->put('main', $adm->id);
                return redirect()->route('clients.index')->with([
                    'success'=>'Olá',
                ]);
            }
            return redirect()->route('orders.index')->with([
                'success'=>'Olá',
            ]);
        }

        $tec = auth()->user()->tec()->first();

        if ($tec) {
            return redirect()->route('notes.index')->with([
                'success'=>'Olá',
            ]);
        }

        $sup = auth()->user()->sup()->first();

        if ($sup) {
            return redirect()->route('tec_on')->with([
                'success'=>'Olá',
            ]);
        }
        
        if (!isset($user_cat->id)) {
            return redirect()->route('login.index')->withErrors(['error' => 'Credenciais inválidas']);
        }

    }

    public function destroy()
    {
        Auth::logout();
        return redirect()->route('login.index');
    }
}

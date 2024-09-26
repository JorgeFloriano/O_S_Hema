<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\NoteTec;
use App\Models\Sup;
use App\Models\Tec;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function add() {

        // $user = new User();
        // $user->name = 'João Pedro';
        // $user->function = 'Adm 01';
        // $user->email = 'joaopedro@hema.com.br';
        // $user->password = Hash::make('j0a0pedr0123');
        // $user->save();
        // echo 'user saved';

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

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5'
        ], [
            'email.required' => 'Digite seu e-mail',
            'password.required' => 'Digite sua senha'
            ]
        );

        $credentials = $request->only('email', 'password');
        $authenticated = Auth::attempt($credentials);

        if (!$authenticated) {
            return redirect()->route('login.index')->withErrors(['error' => 'Credenciais inválidas']);
        }
        
        $adm = auth()->user()->adm()->first();

        if ($adm) {
            if ($adm->main) {
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

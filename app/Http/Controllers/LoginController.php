<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\NoteTec;
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

        // $user2 = new User();
        // $user2->name = 'Janete Moura';
        // $user2->function = 'Téc. Admin';
        // $user2->email = 'janetemoura@hema.com.br';
        // $user2->password = Hash::make('janetem0ura123');
        // $user2->save();

        // $user3 = new User();
        // $user3->name = 'Mary Jane';
        // $user3->function = 'Téc. Admin';
        // $user3->email = 'maryjane@hema.com.br';
        // $user3->password = Hash::make('maryjane123');
        // $user3->save();

        // $user4 = new User();
        // $user4->name = 'Mário Bros';
        // $user4->function = 'Téc. Eletrônico';
        // $user4->email = 'mariobros@hema.com.br';
        // $user4->password = Hash::make('mari0br0s123');
        // $user4->save();

        // $user5 = new User();
        // $user5->name = 'Joel Muller';
        // $user5->function = 'Téc. Mecatrônica';
        // $user5->email = 'joelmuller@hema.com.br';
        // $user5->password = Hash::make('j0elmuller123');
        // $user5->save();

        // $user6 = new User();
        // $user6->name = 'Peeter Parker';
        // $user6->function = 'Téc. Mêcanico';
        // $user6->email = 'peeterparker@hema.com.br';
        // $user6->password = Hash::make('peeterparker123');
        // $user6->save();

        // echo 'user saved';

        // $c_u2 = new Adm();
        // $c_u2->user_id = 2;
        // $c_u2->main = 0;
        // $c_u2->save();

        // $c_u2 = new NoteTec();
        // $c_u2->note_id = 2;
        // $c_u2->tec_id = 3;
        // $c_u2->save();

        // $c_u2 = new NoteTec();
        // $c_u2->note_id = 3;
        // $c_u2->tec_id = 4;
        // $c_u2->save();

        // $c_u2 = new NoteTec();
        // $c_u2->note_id = 6;
        // $c_u2->tec_id = 2;
        // $c_u2->save();

        // $c_u2 = new NoteTec();
        // $c_u2->note_id = 13;
        // $c_u2->tec_id = 1;
        // $c_u2->save();

        $c_u2 = new NoteTec();
        $c_u2->note_id = 4;
        $c_u2->tec_id = 4;
        $c_u2->save();

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

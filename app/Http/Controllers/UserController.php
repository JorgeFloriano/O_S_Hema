<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\Tec;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public readonly User $user;

    public function __construct()
    {
        $this->user = new User();
    }
    
    public function index()
    {
        $users = $this->user->select('id', 'name','function')->get();

        return view('users_list' , ['users' => $users]);
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
        return view('user_create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'email',
            'password' => 'min:5'
        ], [
            'email.email' => 'Digite um e-mail válido',
            'password.min' => 'Digite uma senha com pelo menos 5 caracteres',
        ]);

        if (!$request->adm && !$request->tec) {
            return redirect()->back()->with('message', 'Selecione um acesso para o usuário.');
        }

        if ($request->password !== $request->confirm_pass) {
            return redirect()->back()->with('message', 'A senha digitada tem que ser identica à confirmação.');
        }



        $user_cr = $this->user->create([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'function' => $request->input('function'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        if ($request->adm && $user_cr) {
            $adm_cr = Adm::create([
                'user_id' => $user_cr->id,
                'main' => 0,
            ]);
        }

        if ($request->tec && $user_cr) {
            $tec_cr = Tec::create([
                'user_id' => $user_cr->id,
                'on_call' => 0,
            ]);
        }

        if ($user_cr) {
            return redirect()->route('users.index')->with('message', 'Usuário adm cadastrado com sucesso.');
        }
        return redirect()->route('users.index')->with('message', 'Erro ao cadastrar usuário.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('user_delete', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {

        $tec_checked = '';
        if (isset($user->tec)) {
            $tec_checked = 'checked';
        }

        $adm_checked = '';
        if (isset($user->adm)) {
            $adm_checked = 'checked';
        }

        return view('user_edit', [
            'user' => $user,
            'adm_checked' => $adm_checked,
            'tec_checked' => $tec_checked,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'email' => 'email',
            'password' => 'min:5'
        ], [
            'email.email' => 'Digite um e-mail válido',
            'password.min' => 'Digite uma senha com pelo menos 5 caracteres',
        ]);
        
        $updated = $this->user->where('id', $id)->update($request->except([
            '_token',
            '_method',
            'password',
            'confirm_pass',
            'tec',
            'adm'
        ]));

        if (!$updated) {
            return redirect()->back()->with('message', 'Erro ao atualizar cadastro de usuário.');
        }

        if (!$request->input('tec') && !$request->input('adm')) {
            return redirect()->back()->with('message', 'O usuário deve ter pelo menos um acesso.');
        }

        if ((!$request->input('password') && $request->input('confirm_pass')) || ($request->input('password') && !$request->input('confirm_pass'))) {
            return redirect()->back()->with('message', 'Para alterar a senha, preencha os campos \'Senha\' e \'Confirmação de Senha\'.');
        }

        if ($request->input('password') == $request->input('confirm_pass')) {
            $user = $this->user->where('id', $id)->first();

            $user->password = Hash::make($request->input('password'));
            $user->save();
        } else {
            return redirect()->back()->with('message', 'A senha digitada deve ser identica à confirmação.');
        }

        if ($request->input('tec') && !isset(User::where('id', $id)->first()->tec)) {
            $tec_cr = Tec::create([
                'user_id' => $id,
                'on_call' => 0,
            ]);

            if (!$tec_cr) {
                return redirect()->back()->with('message', 'Erro ao liberar acesso de técnico.'); 
            }
        }
        
        if (!$request->input('tec') && isset(User::where('id', $id)->first()->tec)) {
            $tec_dl = Tec::where('user_id', $id)->delete();

            if (!$tec_dl) {
                return redirect()->back()->with('message', 'Erro ao remover acesso de técnico.'); 
            }
        }

        if ($request->input('adm') && !isset(User::where('id', $id)->first()->adm)) {
            $adm_cr = Adm::create([
               'user_id' => $id,
               'main' => 0,
            ]);
           
            if (!$adm_cr) {
               return redirect()->back()->with('message', 'Erro ao liberar acesso de administrador.');
            }
        }

        if (!$request->input('adm') && isset(User::where('id', $id)->first()->adm)) {
            $adm_dl = Adm::where('user_id', $id)->delete();

            if (!$adm_dl) {
                return redirect()->back()->with('message', 'Erro ao remover acesso de administrador.'); 
            }
        } 

        return redirect()->back()->with('message', 'Cadastro atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        if (Adm::where('user_id', $id)->get()) {
            Adm::where('user_id', $id)->delete();
        }

        if (Tec::where('user_id', $id)->get()) {
            Tec::where('user_id', $id)->delete();
        }

        $deleted = $this->user->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('users.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('users.index')->with('message', 'Erro ao deletar cadastro .');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\Sup;
use App\Models\Tec;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public readonly User $user;
    public $m; // user is adm main or not
    public $s; // user is supervisor or not
    
    public function __construct()
    {
        $this->user = new User();
        if (isset(auth()->user()->adm)) {   
            $this->m = auth()->user()->adm()->first()->main;
        }
        $this->s = auth()->user()->sup()->first();
    }
    
    public function index()
    {
        if (!$this->m) {
            return view('login');
        }

        $users = $this->user->select('id', 'name','function')->simplePaginate(10);

        return view('users_list' , ['users' => $users]);
    }

    public function tec_on()
    {
        if (!$this->m && !$this->s) {
            return view('login');
        }

        $tecs = Tec::simplePaginate(10);

        session()->put('tecs', $tecs);

        return view('tec_on', [
            'tecs' => $tecs
        ]);
    }

    public function tec_on_update(Request $request)
    {
        if (!$this->m && !$this->s) {
            return view('login');
        }

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
        if (!$this->m) {
            return view('login');
        }

        return view('user_create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->m) {
            return view('login');
        }

        $request->validate([
            'email' => 'email|unique:users',
            'password' => 'min:5|unique:users'
        ], [
            'email.email' => 'Digite um e-mail válido',
            'password.min' => 'Digite uma senha com pelo menos 5 caracteres',
            'email.unique' => 'O e-mail digitado está em uso, por favor escolha outro.',
            'password.unique' => 'A senha digitada está em uso, por favor escolha outra.',
        ]);

        if (!$request->adm && !$request->tec && !$request->sup) {
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

        if ($request->sup && $user_cr) {
            $sup_cr = Sup::create([
                'user_id' => $user_cr->id,
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
        if (!$this->m) {
            return view('login');
        }

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

        $sup_checked = '';
        if (isset($user->sup)) {
            $sup_checked = 'checked';
        }

        return view('user_edit', [
            'user' => $user,
            'adm_checked' => $adm_checked,
            'tec_checked' => $tec_checked,
            'sup_checked' => $sup_checked
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!$this->m) {
            return view('login');
        }

        if ($request->input('password')) {
            $request->validate([
                'password' => 'min:5'
            ], [
                'password.min' => 'Digite uma senha com pelo menos 5 caracteres',
            ]);
        }

        $email = $this->user->find($id)->email;

        if ($request->email != $email) {
            $request->validate([
                'email' => 'email|unique:users',
            ], [
                'email.email' => 'Digite um e-mail válido',
                'email.unique' => 'O e-mail digitado está em uso, por favor escolha outro.',
            ]);
        }
        
        $updated = $this->user->where('id', $id)->update($request->except([
            '_token',
            '_method',
            'password',
            'confirm_pass',
            'tec',
            'adm',
            'sup'
        ]));

        if (!$updated) {
            return redirect()->back()->with('message', 'Erro ao atualizar cadastro de usuário.');
        }

        if (!$request->input('tec') && !$request->input('adm') && !$request->input('sup')) {
            return redirect()->back()->with('message', 'O usuário deve ter pelo menos um acesso.');
        }

        if ((!$request->input('password') && $request->input('confirm_pass')) || ($request->input('password') && !$request->input('confirm_pass'))) {
            return redirect()->back()->with('message', 'Para alterar a senha, preencha os campos \'Senha\' e \'Confirmação de Senha\'.');
        }

        if ($request->password == $request->confirm_pass && $request->password != '') {
            $user = $this->user->where('id', $id)->first();

            $user->password = Hash::make($request->input('password'));
            $user->save();
        } else {
            if ($request->password != '' && $request->confirm_pass != '') {
                return redirect()->back()->with('message', 'A senha digitada deve ser identica à confirmação.');
            }
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

        if ($request->input('sup') && !isset(User::where('id', $id)->first()->sup)) {
            $sup_cr = Sup::create([
                'user_id' => $id
            ]);

            if (!$sup_cr) {
                return redirect()->back()->with('message', 'Erro ao liberar acesso de supervisor.'); 
            }
        }

        if (!$request->input('sup') && isset(User::where('id', $id)->first()->sup)) {
            $sup_dl = Sup::where('user_id', $id)->delete();

            if (!$sup_dl) {
                return redirect()->back()->with('message', 'Erro ao remover acesso de supervisor.'); 
            }
        }

        return redirect()->back()->with('message', 'Cadastro atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$this->m) {
            return view('login');
        }

        if (Adm::where('user_id', $id)->get()) {
            Adm::where('user_id', $id)->delete();
        }

        if (Tec::where('user_id', $id)->get()) {
            Tec::where('user_id', $id)->delete();
        }

        if (Sup::where('user_id', $id)->get()) {
            Sup::where('user_id', $id)->delete();
        }

        $deleted = $this->user->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('users.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('users.index')->with('message', 'Erro ao deletar cadastro .');
    }
}

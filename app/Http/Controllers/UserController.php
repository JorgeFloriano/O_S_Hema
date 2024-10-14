<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCrUserRequest;
use App\Http\Requests\FormUpUserRequest;
use App\Models\Adm;
use App\Models\Sup;
use App\Models\Tec;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

        $admins = Adm::select('user_id')->where('main', 1)->get();

        $users = $this->user->select('id', 'name','function')->whereNotIn('id', $admins)->simplePaginate(10);

        return view('user.users_list' , ['users' => $users]);
    }
    
    //If logged in user is adm main or supervisor, show technician on call list
    public function tec_on()
    {
        if (!$this->m && !$this->s) {
            return view('login');
        }

        $tecs = Tec::simplePaginate(10);

        session()->put('tecs', $tecs);

        return view('user.tec_on', [
            'tecs' => $tecs
        ]);
    }

    // If logged in user is adm main or supervisor, Technician on call update
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

    // If logged in user is adm main, show create user form
    public function create()
    {
        if (!$this->m) {
            return view('login');
        }

        return view('user.user_create');
    }

    // If logged in user is adm main, validate and create a new user
    public function store(FormCrUserRequest $request)
    {
        if (!$this->m) {
            return view('login');
        }

        $request->validated();
        $email = $request->username.'@hema.com.br';

        // Create new user
        $user_cr = $this->user->create([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'function' => $request->input('function'),
            'username' => $request->input('username'),
            'email' => $email,
            'password' => Hash::make($request->input('password')),
        ]);

        // If new user was created
        if ($user_cr) {
            // If adm option is selected, makes available admin access
            if ($request->adm) {
                $adm_cr = Adm::create([
                    'user_id' => $user_cr->id,
                    'main' => 0,
                ]);
            }
    
            // If sup option is selected, makes available supervisor access
            if ($request->sup) {
                $sup_cr = Sup::create([
                    'user_id' => $user_cr->id,
                ]);
            }
    
            // If tec option is selected, makes available technician access
            if ($request->tec) {
                $tec_cr = Tec::create([
                    'user_id' => $user_cr->id,
                    'on_call' => 0,
                ]);
            }

            return redirect()->route('users.index')->with('message', 'Usuário adm cadastrado com sucesso.');
        }

        return redirect()->route('users.index')->with('message', 'Erro ao cadastrar usuário.');
    }

    // Shows the form to delete the user registration
    public function show(User $user)
    {
        if (!$this->m) {
            return view('login');
        }

        return view('user.user_delete', ['user' => $user]);
    }

    // Shows the form to edit the user registration
    public function edit(User $user)
    {
        if (!$this->m) {
            return view('login');
        }

        // Checks if the user has any access and this will be selected
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

        return view('user.user_edit', [
            'user' => $user,
            'adm_checked' => $adm_checked,
            'tec_checked' => $tec_checked,
            'sup_checked' => $sup_checked
        ]);
    }

    
    // If logged in user is adm main, validate and update the user registration
    public function update(Request $request, string $id)
    {
        if (!$this->m) {
            return view('login');
        }

        $min = $request->password && $request->password_confirmation ? 'min:5' : 'min:0';

        Validator::make($request->all(), [
            'name' => 'required|max:20',
            'surname' => 'max:20',
            'function' => 'required|max:20',
            'username' => [Rule::unique('users')->ignore($id), 'min:10', 'max:100'],
            'password' => [$min, 'confirmed']
        ], [
            'username.unique' => 'O nome de usúario digitado está em uso, por favor escolha outro.',
            'password.min' => 'Digite uma senha com pelo menos 5 caracteres',
            'password.confirmed' => 'As senhas digitadas deveriam ser identicas.',
        ])->validate();

        //If the password field is filled, validate it
        if ($request->input('password')) {
            $request->validate([
                'password' => 'min:5'
            ], [
                'password.min' => 'Digite uma senha com pelo menos 5 caracteres',
            ]);
        }
       
        $updated = $this->user->where('id', $id)->update($request->except([
            '_token',
            '_method',
            'password',
            'password_confirmation',
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

        // Verify if the password and passwordconfirmation fields are equal and not empty
        if ($request->password == $request->password_confirmation && $request->password != '') {

            // Update the password
            $user = $this->user->where('id', $id)->first();

            $user->password = Hash::make($request->input('password'));
            $user->save();
        }

        // If the user not has a technician access and the tec field is filled, create one
        if ($request->input('tec') && !isset(User::where('id', $id)->first()->tec)) {
            $tec_cr = Tec::create([
                'user_id' => $id,
                'on_call' => 0,
            ]);

            // Return an error message.
            if (!$tec_cr) {
                return redirect()->back()->with('message', 'Erro ao liberar acesso de técnico.'); 
            }
        }
        
        // If the user has a technician access and the tec field is not filled, remove it
        if (!$request->input('tec') && isset(User::where('id', $id)->first()->tec)) {
            $tec_dl = Tec::where('user_id', $id)->delete();

            // Return an error message.
            if (!$tec_dl) {
                return redirect()->back()->with('message', 'Erro ao remover acesso de técnico.'); 
            }
        }

        // If the user not has an administrator access and the adm field is filled, create one
        if ($request->input('adm') && !isset(User::where('id', $id)->first()->adm)) {
            $adm_cr = Adm::create([
               'user_id' => $id,
               'main' => 0,
            ]);
           
            // Return an error message.
            if (!$adm_cr) {
               return redirect()->back()->with('message', 'Erro ao liberar acesso de administrador.');
            }
        }

        // If the user has an administrator access and the adm field is not filled, remove it
        if (!$request->input('adm') && isset(User::where('id', $id)->first()->adm)) {
            $adm_dl = Adm::where('user_id', $id)->delete();

            // Return an error message.
            if (!$adm_dl) {
                return redirect()->back()->with('message', 'Erro ao remover acesso de administrador.'); 
            }
        }

        // If the user not has a supervisor access and the sup field is filled, create one
        if ($request->input('sup') && !isset(User::where('id', $id)->first()->sup)) {
            $sup_cr = Sup::create([
                'user_id' => $id
            ]);

            // Return an error message.
            if (!$sup_cr) {
                return redirect()->back()->with('message', 'Erro ao liberar acesso de supervisor.'); 
            }
        }

        // If the user has a supervisor access and the sup field is not filled, remove it
        if (!$request->input('sup') && isset(User::where('id', $id)->first()->sup)) {
            $sup_dl = Sup::where('user_id', $id)->delete();

            // Return an error message.
            if (!$sup_dl) {
                return redirect()->back()->with('message', 'Erro ao remover acesso de supervisor.'); 
            }
        }

        // Return a success message
        return redirect()->back()->with('message', 'Cadastro atualizado com sucesso.');
    }

    // If logged in user is adm main, delete the selected user
    public function destroy(string $id)
    {
        if (!$this->m) {
            return view('login');
        }

        // Delete all accesses of the selected user
        if (Adm::where('user_id', $id)->get()) {Adm::where('user_id', $id)->delete();}
        if (Tec::where('user_id', $id)->get()) {Tec::where('user_id', $id)->delete();}
        if (Sup::where('user_id', $id)->get()) {Sup::where('user_id', $id)->delete();}

        // Delete the selected user
        $deleted = $this->user->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('users.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('users.index')->with('message', 'Erro ao deletar cadastro .');
    }
}

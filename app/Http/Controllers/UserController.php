<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCrUserRequest;
use App\Models\Adm;
use App\Models\Cli;
use App\Models\Client;
use App\Models\Sup;
use App\Models\Tec;
use App\Models\User;
use App\Rules\StrongPass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;

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

        // Get the main admins
        $admins = Adm::select('user_id')->where('main', 1)->get();

        //Get the Default client users that are created for the Admin Client user trought the clients app hema
        $users_cli_default = Cli::select('user_id')->where('is_admin', null)->get();
        
        // Get all users except the main admins and the default client users
        $users = $this->user
            ->select('id', 'name', 'function')
            ->whereNotIn('id', $admins)
            ->whereNotIn('id', $users_cli_default)
            ->orderBy('name') // Order by the 'name' column
            ->simplePaginate(20);

        return view('user.users_list' , ['users' => $users]);
    }
    
    //If logged in user is adm main or supervisor, show technician on call list
    public function tec_on()
    {
        if (!$this->m && !$this->s) {
            return view('login');
        }

        $tecs = Tec::join('users', 'tecs.user_id', '=', 'users.id')
        ->whereNotIn('tecs.user_id', [1, 2, 9999, 0])
        ->orderBy('users.name') // Order by the user's name
        ->select('tecs.*') // Select only the Tec columns
        ->simplePaginate(20);
        
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

        // Get id and name of all clients order by name
        $clients = Client::select('id', 'name')->orderBy('name')->get();

        return view('user.user_create' , [
            'clients' => $clients
        ]);
    }

    // If logged in user is adm main, validate and create a new user
    public function store(FormCrUserRequest $request)
    {
        if (!$this->m) {
            return view('login');
        }

        if ($request->user_client) {
            $request->validate([
                'client_id' => [
                    'required_if:user_client,true', 
                    Rule::exists('clients', 'id'), 
                    'unique:clis,client_id'
                ],
                'adm' => 'boolean',
                'tec' => 'boolean', 
                'sup' => 'boolean',
                'user_client' => [
                    'boolean',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($value && ($request->adm || $request->tec || $request->sup)) {
                            $fail('Um usuário cliente não pode ter os acessos de colaboradores Hema');
                        }

                        if (!isset($value) && !isset($request->adm) && !isset($request->tec) && !isset($request->sup)) {
                            $fail('Selecione pelo menos um acesso para o usuário.');
                        }
                    }
                ],
            ], [
                'client_id.required_if' => 'Selecione um cliente para o usuário.',
                'client_id.exists' => 'O cliente selecionado não existe.',
                'client_id.unique' => 'O cliente selecionado já possui um usuário cadastrado.',
                '*.boolean' => 'Os campos de perfis de acesso devem ser apenas marcados ou desmarcados.',
            ]);
        }

        $request->validated();
        $email = $request->username.'@hemasystem.com.br';

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

            // If user_client option is selected, makes available client access
            if ($request->user_client) {
                $cli_cr = Cli::create([
                    'user_id' => $user_cr->id,
                    'is_admin' => true,
                    'can_create_sat' => true,
                    'can_see_sat' => true,
                    'client_id' => $request->client_id
                ]);

                if (!$cli_cr) {
                    $user_cr->delete();
                    return redirect()->route('users.index')->with('message', 'Erro ao cadastrar Usuário Cliente.');
                }
            }


            // If adm option is selected, makes available admin access
            if ($request->adm) {
                $adm_cr = Adm::create([
                    'user_id' => $user_cr->id,
                    'main' => 0,
                    'cli' => $request->cli ? 1 : 0,
                ]);

                if (!$adm_cr) {
                    $user_cr->delete();
                    return redirect()->route('users.index')->with('message', 'Erro ao cadastrar Usuário Administrador.');
                }
            }
    
            // If sup option is selected, makes available supervisor access
            if ($request->sup) {
                $sup_cr = Sup::create([
                    'user_id' => $user_cr->id,
                ]);

                if (!$sup_cr) {
                    $user_cr->delete();
                    return redirect()->route('users.index')->with('message', 'Erro ao cadastrar Usuário Supervisor.');
                }
            }
    
            // If tec option is selected, makes available technician access
            if ($request->tec) {
                $tec_cr = Tec::create([
                    'user_id' => $user_cr->id,
                    'on_call' => 0,
                ]);

                if (!$tec_cr) {
                    $user_cr->delete();
                    return redirect()->route('users.index')->with('message', 'Erro ao cadastrar Usuário Técnico.');
                }
            }

            return redirect()->route('users.index')->with('message', 'Usuário adm cadastrado com sucesso.');
        }

        return redirect()->route('users.index')->with('message', 'Erro ao cadastrar usuário.');
    }

    // Shows the form to delete the user registration
    public function show($user)
    {
        // Decrypt the user id
        try {
            $user = $this->user->find(Crypt::decryptString($user));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        // If user main try to edit another user main return false
        if (!auth()->user()->editUserPermission($user->id)) {
            return view('login');
        }
       
        return view('user.user_delete', ['user' => $user]);
    }

    // Shows the form to edit the user registration
    public function edit($user)
    {
    
        // Decrypt the user id
        try {
            $user = $this->user->find(Crypt::decryptString($user));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        // If user main try to edit another user main return false
        if (!auth()->user()->editUserPermission($user->id)) {
            return redirect()->back()->with('message', 'Sem permissão para editar este usuário.');

        }
        
        // Checks if the user has any access and this will be selected
        $tec_checked = '';
        if (isset($user->tec)) {
            $tec_checked = 'checked';
        }

        $adm_checked = '';
        $cli_checked = '';
        $cli_disabled = 'disabled';
        if (isset($user->adm)) {
            $adm_checked = 'checked';
            $cli_disabled = '';
            if ($user->adm->cli) {
                $cli_checked = 'checked';
            }
        }

        $sup_checked = '';
        if (isset($user->sup)) {
            $sup_checked = 'checked';
        }

        // Get id and name of all clients order by name
        $clients = Client::select('id', 'name')->orderBy('name')->get();

        $hema_profiles_display = 'block';
        $client_select_display = 'none';
        $user_client_checked = '';
        $client_selected = '';
        if (isset($user->cli)) {
            $user_client_checked = 'checked';
            $hema_profiles_display = 'none';
            $client_select_display = 'block';

            $client_id = $user->cli->client_id;
            $client_name = ($clients->where('id', $client_id)->first()->name);
            $client_selected = $client_name.' - ['.$client_id.']';
        }

        return view('user.user_edit', [
            'user' => $user,
            'clients' => $clients,
            'adm_checked' => $adm_checked ?? '',
            'cli_checked' => $cli_checked ?? '',
            'tec_checked' => $tec_checked ?? '',
            'sup_checked' => $sup_checked ?? '',
            'cli_disabled' => $cli_disabled ?? '',
            'user_client_checked' => $user_client_checked ?? '',
            'hema_profiles_display' => $hema_profiles_display ?? '',
            'client_select_display' => $client_select_display ?? '',
            'client_selected' => $client_selected ?? '',
        ]);
    }

    
    // If logged in user is adm main, validate and update the user registration
    public function update(Request $request, string $id)
    {
        // If user main try to edit another user main return false
        if (!auth()->user()->editUserPermission($id)) {
            return view('login');
        }

        // If the user is going to update the password, it must have at least 8 characters.
        $min = $request->password && $request->password_confirmation ? 'min:8' : 'min:0';

        // User that is updated is adm main
        $main_id = 0;
        if (isset(User::where('id', $id)->first()->adm)) {
            $main_id = User::where('id', $id)->first()->adm()->first()->main;
        }

        // User that is updated is client
        $is_user_client = User::where('id', $id)->first()->cli;
        if (isset($is_user_client)) {
            $client_id = User::where('id', $id)->first()->cli()->first()->client_id;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:20',
            'surname' => 'max:20',
            'function' => 'max:20',
            'username' => ['required' ,Rule::unique('users')->ignore($id), 'min:10', 'max:100'],
            'password' => [$min, 'confirmed'],
            'adm' => 'boolean',
            'tec' => 'boolean', 
            'sup' => 'boolean',
            'user_client' => [
                'boolean',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && ($request->adm || $request->tec || $request->sup)) {
                        $fail('Um usuário cliente não pode ter os acessos de colaboradores Hema');
                    }

                    if (!isset($value) && !isset($request->adm) && !isset($request->tec) && !isset($request->sup)) {
                        $fail('Selecione pelo menos um acesso para o usuário.');
                    }
                }
            ],
        ], [
            'name.required' => 'O campo nome deve ser preenchido.',
            'name.max' => 'O campo nome deve ter no máximo 20 caracteres.',
            'surname.max' => 'O campo sobrenome deve ter no máximo 20 caracteres.',
            'function.max' => 'O campo função deve ter no.maxcdn 20 caracteres.',
            'username.required' => 'O campo nome de usuário deve ser preenchido.',
            'username.unique' => 'O nome de usúario digitado está em uso, por favor escolha outro.',
            'username.min' => 'O campo nome de usuário deve ter pelo menos 10 caracteres.',
            'username.max' => 'O campo nome de usuário deve ter no.maxcdn 100 caracteres.',
            'password.min' => 'Digite uma senha com pelo menos 8 caracteres',
            'password.confirmed' => 'As senhas digitadas devem ser identicas.',
            'client_id.required' => 'Selecione um cliente para o usuário.',
            'client_id.exists' => 'O cliente selecionado não existe.',
            'client_id.unique' => 'O cliente selecionado já possui um usuário cadastrado.',
            '*.boolean' => 'Os campos de perfis de acesso devem ser apenas marcados ou desmarcados.',
        ]);

        // Add custom messages for the conditional validation
        $validator->sometimes('client_id', [
            'required',
            'exists:clients,id',
            Rule::unique('clis', 'client_id')->ignore($client_id ?? 0, 'client_id'),
        ], function ($input) {
            return $input->user_client == true;
        });

        $validator->validate();

        // If the password field is filled, validate it
        if ($request->input('password')) {
            if (!$main_id) {
                $request->validate([
                    'password' => 'min:8'
                ], [
                    'password.min' => 'Digite uma senha com pelo menos 8 caracteres',
                ]);
            } else {
                $request->validate(['password' => [new StrongPass]]);
            }
        }
       
        $updated = $this->user->where('id', $id)->update($request->except([
            '_token',
            '_method',
            'password',
            'password_confirmation',
            'tec',
            'adm',
            'cli',
            'sup',
            'user_client',
            'client_id',
            'client'
        ]));

        if (!$updated) {
            return redirect()->back()->with('message', 'Erro ao atualizar cadastro de usuário.');
        }

        if ((
            !isset($is_user_client) &&
            !$request->input('tec') && 
            !$request->input('adm') && 
            !$request->input('sup')) && 
            !$main_id) {
            return redirect()->back()->with('message', 'O usuário deve ter pelo menos um acesso.');
        }

        // Verify if the password and passwordconfirmation fields are equal and not empty--------
        if ($request->password == $request->password_confirmation && $request->password != '') {

            // Update the password
            $user = $this->user->where('id', $id)->first();

            $user->password = Hash::make($request->input('password'));
            $user->save();
        }

        // If the user not has a technician access and the tec field is filled, create one---------
        if ($request->input('tec') && !isset(User::where('id', $id)->first()->tec)) {

            // Check if the user has a technician access deleted
            $tec_deleted = Tec::where('user_id', $id)->withTrashed()->first();

            if ($tec_deleted) {
                $tec = $tec_deleted->restore();
            } else {
                $tec = Tec::create([
                    'user_id' => $id,
                    'on_call' => 0,
                ]);
            }
            
            // Return an error message.
            if (!$tec) {
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

        // If the user not has an administrator access and the adm field is filled, create one---------
        $adm = User::where('id', $id)->first()->adm;

        if ($request->adm) {
            if (!isset($adm)) {

                $cli_mat = 0;
                if ($request->cli && !isset($adm->cli)) {
                    $cli_mat = 1;
                }

                // Check if the user has a administrator access deleted
                $adm_deleted = Adm::where('user_id', $id)->withTrashed()->first();

                if ($adm_deleted) {
                    $new_adm = $adm_deleted->restore();
                } else {
                    $new_adm = Adm::create([
                        'user_id' => $id,
                        'main' => 0,
                        'cli' => $cli_mat
                    ]);
                }
               
                // Return an error message.
                if (!$new_adm) {
                   return redirect()->back()->with('message', 'Erro ao liberar acesso de administrador.');
                } 
            } else {
                
                $cli_up = Adm::find($adm->id);
                $cli_up->cli = $request->cli ? 1 : 0;
                $cli_up->save();

                if (!$cli_up) {
                    return redirect()->back()->with('message', 'Erro ao atualizar acesso de administrador.');
                }
            }
        }

        // If the user has an administrator access, the adm field is not filled and the user is not main, remove administrator access
        if (!$request->input('adm') && isset(User::where('id', $id)->first()->adm) && !$main_id) {

            $adm_dl = Adm::where('user_id', $id)->delete();

            // Return an error message.
            if (!$adm_dl) {
                return redirect()->back()->with('message', 'Erro ao remover acesso de administrador.'); 
            }
        }

        // If the user not has a supervisor access and the sup field is filled, create one----------
        if ($request->input('sup') && !isset(User::where('id', $id)->first()->sup)) {

            $sup_deleted = Sup::where('user_id', $id)->withTrashed()->first();

            if ($sup_deleted) {
                $new_sup = $sup_deleted->restore();
            } else {
                $new_sup = Sup::create([
                    'user_id' => $id,
                ]);
            }

            // Return an error message.
            if (!$new_sup) {
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

        // // If the user not has a client access and the user_client field is filled, create one--------------
        // if ($request->input('user_client') && !isset(User::where('id', $id)->first()->cli)) {

        //     $cli_deleted = Cli::where('user_id', $id)->withTrashed()->first();

        //     // Check if the user has a client access deleted
        //     if ($cli_deleted) {
        //         // Restore the deleted client access
        //         $new_cli = $cli_deleted->restore();
        //     } else {
        //         // Create a new client access
        //         $new_cli = Cli::create([
        //             'user_id' => $id,
        //             'is_admin' => true,
        //             'client_id' => $request->client_id
        //         ]);
        //     }
           
        //     // Return an error message.
        //     if (!$new_cli) {
        //         return redirect()->back()->with('message', 'Erro ao liberar acesso de cliente.'); 
        //     }
        // }

        // // If the user has a client access and the user_client field is filled, update it
        // if ($request->input('user_client') && isset(User::where('id', $id)->first()->cli)) {
        //     $cli_up = Cli::find(User::where('id', $id)->first()->cli->id);
        //     $cli_up->client_id = $request->client_id;
        //     $cli_up->save();
        // }

        // // If the user has a client access and the user_client field is not filled, remove it
        // if (!$request->input('user_client') && isset(User::where('id', $id)->first()->cli)) {
        //     $cli_dl = Cli::where('user_id', $id)->delete();

        //     // Return an error message.
        //     if (!$cli_dl) {
        //         return redirect()->back()->with('message', 'Erro ao remover acesso de cliente.'); 
        //     }
        // }

        // Return a success message
        return redirect()->back()->with('message', 'Cadastro atualizado com sucesso.');
    }

    // If logged in user is adm main, delete the selected user
    public function destroy(string $id)
    {
        // If user main try to edit another user main return false
        if (!auth()->user()->editUserPermission($id)) {
            return view('login');
        }

        // Delete all accesses of the selected user
        // if (Adm::where('user_id', $id)->get()) {Adm::where('user_id', $id)->delete();}
        // if (Tec::where('user_id', $id)->get()) {Tec::where('user_id', $id)->delete();}
        // if (Sup::where('user_id', $id)->get()) {Sup::where('user_id', $id)->delete();}
        // if (Cli::where('user_id', $id)->get()) {Cli::where('user_id', $id)->delete();}

        // Delete the selected user and all of his accesses
        $deleted = $this->user->find($id)->CompletelyDelete();

        if ($deleted) {
            return redirect()->route('users.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('users.index')->with('message', 'Erro ao deletar cadastro .');
    }
}

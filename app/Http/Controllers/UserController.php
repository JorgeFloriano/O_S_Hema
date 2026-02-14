<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCrUserRequest;
use App\Models\Adm;
use App\Models\Cli;
use App\Models\Client;
use App\Models\Sup;
use App\Models\Tec;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Requests\FormUpdateUserRequest;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public readonly User $user;
    public $m; // user is adm main or not
    public $s; // user is supervisor or not

    public function __construct()
    {
        if (isset(auth()->user()->adm)) {
            $this->m = auth()->user()->adm()->first()->main;
        }
        $this->s = auth()->user()->sup()->first();

        $this->user = Auth::user();
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
        $users = User::whereNotIn('id', [1, 2, 9999, 0])
            ->select('id', 'name', 'function')
            ->whereNotIn('id', $admins)
            ->whereNotIn('id', $users_cli_default)
            ->orderBy('name') // Order by the 'name' column
            ->simplePaginate(20);

        return view('user.users_list', ['users' => $users]);
    }

    // If logged in user is adm main or supervisor, show technician on call list
    public function tec_on()
    {
        if (!$this->m && !$this->s) {
            return view('login');
        }

        $tecs = Tec::with(['emergencyClients', 'emergencyOrder:id,finished,tec_id'])
            // Carregamos apenas as colunas id, finished e tec_id da SAT para economizar memória
            ->join('users', 'tecs.user_id', '=', 'users.id')
            ->whereNotIn('tecs.user_id', [1, 2, 9999, 0])
            ->orderBy('users.name')
            ->select('tecs.*')
            ->simplePaginate(20);

        // Adiciona a lógica de "busy" (ocupado) para cada técnico
        $tecs->getCollection()->transform(function ($tec) {
            $order = $tec->emergencyOrder;

            // Condições: 
            // 1. Existe uma SAT vinculada
            // 2. A SAT não está finalizada
            // 3. O técnico da SAT é o próprio técnico (conferência de integridade)
            $tec->busy = ($order && !$order->finished && $order->tec_id == $tec->id);

            return $tec;
        });

        session()->put('tecs', $tecs);
        session()->put('reference_router_back', 'tec_on');

        $clients = Client::select('id', 'name')->orderBy('name')->get();

        return view('user.tec_on', compact('tecs', 'clients'));
    }

    // If logged in user is adm main or supervisor, Technician on call update
    public function tec_on_update(Request $request)
    {
        if (!$this->m && !$this->s) {
            return view('login');
        }

        // Buscamos os técnicos novamente para garantir que temos os objetos do Eloquent
        $tecIds = session('tecs')->pluck('id');
        $tecs = Tec::whereIn('id', $tecIds)->get();

        foreach ($tecs as $tec) {
            // 1. Atualiza Status Sobreaviso
            $tec->on_call = $request->has('tec' . $tec->id) ? 1 : 0;
            $tec->save();

            // 2. Atualiza Vínculos com Clientes (Tabela Pivot)
            // O input 'clients' vem como um array multidimensional: clients[tec_id][client_id]
            $clientIds = $request->input("clients.{$tec->id}", []);
            $tec->emergencyClients()->sync($clientIds);
        }

        return redirect()->back();
    }

    // If logged in user is supervisor, reset all emergencies for all technicians
    public function tec_on_stop_all_notifications()
    {
        if ($this->m) {
            auth()->user()->resetAllEmergencies();
            return redirect()->back()->with('message', 'Todas as notificações de emergência foram imterrompidas.');
        }

        return view('login');
    }

    // If logged in user is adm main, show create user form
    public function create()
    {
        if (!$this->m) {
            return view('login');
        }

        // Get id and name of all clients order by name
        $clients = Client::select('id', 'name')->orderBy('name')->get();

        return view('user.user_create', [
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
        $email = $request->username . '@hemasystem.com.br';

        // Create new user
        $user_cr = User::create([
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
            $user = User::find(Crypt::decryptString($user));
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
    public function edit(string $user)
    {

        // Decrypt the user id
        try {
            $user = User::find(Crypt::decryptString($user));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        // If user main try to edit another user main return false
        if (!$this->user->editUserPermission($user->id)) {
            return redirect()->back()->with('message', 'Sem permissão para editar este usuário.');
        }

        // Carrega o usuário com suas permissões já vinculadas
        $user = User::with('permissions')->findOrFail($user->id);

        // Se for um usuário cliente, a lógica de exibição pode ser diferente
        // Mas para os colaboradores Hema, precisamos preparar os dados
        $clients = Client::select('id', 'name')->orderBy('name')->get();

        return view('user.user_edit', compact('user', 'clients'));
    }

    // If logged in user is adm main, validate and update the user registration
    public function update(FormUpdateUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);

        // Atualiza os dados básicos
        $user->update($request->except([
            '_token',
            '_method',
            'password',
            'password_confirmation',
            'permissions',
        ]));

        // Agora $user é o objeto, você pode atualizar a senha
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        if (!$user) {
            return redirect()->back()->with('message', 'Erro ao atualizar cadastro de usuário.');
        }

        // 3. Sincronização de Permissões e Perfis (Adm, Sup, Tec)
        $perms = $request->input('permissions', []);

        // Lógica para sincronizar a tabela pivô (permission_user)
        $syncData = [];
        foreach ($perms as $name => $level) {
            if ($level > 0 && $name !== 'compl_sup_access') {
                $permission = Permission::where('name', $name)->first();
                if ($permission) {
                    $syncData[$permission->id] = ['access_level' => $level];
                }
            }
        }
        $user->permissions()->sync($syncData);

        // 4. Ativação automática dos Models de Perfil
        // Adm
        $hasAdm = collect($perms)->only(['sats', 'users', 'materials', 'clients', 'codes'])->contains(fn($v) => $v > 0);
        if ($hasAdm) {
            Adm::withTrashed()->updateOrCreate(['user_id' => $user->id], ['deleted_at' => null, 'main' => 0]);
        } elseif (!$user->isMainAdm()) {
            $user->adm()?->delete();
        }

        // Sup
        $hasSup = collect($perms)->only(['reopen_sat', 'attach_tec', 'manager_on_call'])->contains(fn($v) => $v > 0);
        if ($hasSup) {
            Sup::withTrashed()->updateOrCreate(['user_id' => $user->id], ['deleted_at' => null]);
        } else {
            $user->sup()?->delete();
        }

        // Tec
        if ((isset($perms['tech_access']) && $perms['tech_access'] > 0) || isset($request->main_adm_tec_access)) {
            Tec::withTrashed()->updateOrCreate(['user_id' => $user->id], ['deleted_at' => null, 'on_call' => 0]);
        } else {
            $user->tec()?->delete();
        }

        return redirect()->back()->with('message', 'Cadastro atualizado com sucesso.');
    }

    // If logged in user is adm main, delete the selected user
    public function destroy(string $id)
    {
        // If user main try to edit another user main return false
        if (!auth()->user()->editUserPermission($id)) {
            return view('login');
        }

        // Delete the selected user and all of his accesses
        $deleted = User::find($id)->CompletelyDelete();

        if ($deleted) {
            return redirect()->route('users.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('users.index')->with('message', 'Erro ao deletar cadastro .');
    }
}

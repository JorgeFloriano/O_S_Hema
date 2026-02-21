<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormCreateUserRequest;
use App\Models\Adm;
use App\Models\Cli;
use App\Models\Client;
use App\Models\Sup;
use App\Models\Tec;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Requests\FormUpdateUserRequest;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public readonly User $auth;

    public function __construct()
    {
        $this->auth = Auth::user();
    }

    public function index()
    {
        Gate::authorize('check-permission', ['users', 1]);

        // Get the main admins
        $admins = Adm::select('user_id')->where('main', 1)->get();

        //Get the Default client users that are created for the Admin Client user trought the clients app hema
        $users_cli_default = Cli::select('user_id')->where('is_admin', null)->get();

        // Get all users except the main admins and the default client users
        $users = User::whereNotIn('id', [1, 2, 9999, 0])
            ->select('id', 'name', 'function')
            ->whereNotIn('id', $admins)
            ->whereNotIn('id', $users_cli_default)
            ->whereNot('id', $this->auth->id)
            ->orderBy('name') // Order by the 'name' column
            ->simplePaginate(20);

        return view('user.users_list', ['users' => $users]);
    }

    // If logged in user is adm main or supervisor, show technician on call list
    public function tec_on()
    {
        Gate::authorize('check-permission', ['manager_on_call', 2]);

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
        Gate::authorize('check-permission', ['manager_on_call', 2]);

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
        Gate::authorize('is-main-adm');

        $this->auth->resetAllEmergencies();
        return redirect()->back()->with('message', 'Todas as notificações de emergência foram imterrompidas.');
    }

    // If logged in user is adm main, show create user form
    public function create()
    {
        Gate::authorize('check-permission', ['users', 2]);

        // Get id and name of all clients order by name
        $clients = Client::select('id', 'name')->orderBy('name')->get();

        return view('user.user_create', [
            'clients' => $clients
        ]);
    }

    public function store(FormCreateUserRequest $request)
    {
        Gate::authorize('check-permission', ['users', 2]);

        try {
            return DB::transaction(function () use ($request) {
                // 1. Criar o Usuário Base
                $user = User::create([
                    'name'     => $request->name,
                    'surname'  => $request->surname,
                    'function' => $request->function,
                    'username' => $request->username,
                    'email'    => $request->username . '@hemasystem.com.br',
                    'password' => Hash::make($request->password),
                ]);

                $hasProfile = false;

                // 2. Lógica para Usuário CLIENTE (Exclusiva)
                if ($request->type_user == 2) {
                    Cli::create([
                        'user_id'        => $user->id,
                        'client_id'      => $request->client_id,
                        'is_admin'       => true,
                        'can_create_sat' => true,
                        'can_see_sat'    => true,
                    ]);
                    $hasProfile = true;
                }

                // 3. Lógica para Usuário HEMA (Permissões e Perfis)
                if ($request->type_user == 1) {
                    // Esta função retorna true se algum perfil (adm, sup ou tec) foi criado
                    $hasProfile = $this->syncHemaPermissions($user, $request->input('permissions', []));
                }

                // --- TRAVA DE SEGURANÇA FINAL ---
                if (!$hasProfile) {
                    throw new \Exception('O usuário deve possuir ao menos um perfil ativo (Cliente, Adm, Sup ou Tec).');
                }

                return redirect()->route('users.index')
                    ->with('message', 'Usuário cadastrado com sucesso.');
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Erro ao cadastrar: ' . $e->getMessage());
        }
    }

    private function syncHemaPermissions($user, array $perms): bool
    {
        Gate::authorize('check-permission', ['users', 2]);

        $syncData = [];

        if (!isset($perms['users'])) {
            $perms['users'] = 0;
        }

        // 1. Processa permissões do formulário
        foreach ($perms as $name => $level) {
            $levelToSync = $level;

            // PROTEÇÃO: Se não for MainAdm e estiver mexendo em 'users'
            if (!$this->auth->isMainAdm() && $name === 'users') {
                // Pegamos o nível que o usuário JÁ TINHA no banco
                $currentPerm = $user->permissions->where('name', 'users')->first();
                $levelToSync = $currentPerm ? $currentPerm->pivot->access_level : 0;
            }

            if ($levelToSync > 0 && $name !== 'compl_sup_access') {
                $permission = Permission::where('name', $name)->first();
                if ($permission) {
                    $syncData[$permission->id] = ['access_level' => $levelToSync];
                }
            }
        }

        $isSupervisor = collect($perms)->only(['reopen_sat', 'attach_tec', 'manager_on_call'])->contains(fn($v) => $v > 0);
        $isAdm = collect($perms)->only(['sats', 'materials', 'clients', 'codes', 'users'])->contains(fn($v) => $v > 0);
        $isTec = (isset($perms['tech_access']) && $perms['tech_access'] > 0) || request()->has('main_adm_tec_access');

        // 2. Lógica para SUPERVISOR
        if ($isSupervisor) {
            // Buscamos incluindo deletados. Se não existir, criamos.
            $sup = Sup::withTrashed()->firstOrNew(['user_id' => $user->id]);
            $sup->deleted_at = null; // Garante que saia do lixo
            $sup->save();

            // Injeta Visualização de SATs
            $satPerm = Permission::where('name', 'sats')->first();
            if ($satPerm && (!isset($syncData[$satPerm->id]) || $syncData[$satPerm->id]['access_level'] < 1)) {
                $syncData[$satPerm->id] = ['access_level' => 1];
            }
        } else {
            $user->sup()?->delete();
        }

        // 3. Lógica para ADMINISTRADOR
        if ($isAdm) {
            $adm = Adm::withTrashed()->firstOrNew(['user_id' => $user->id]);
            $adm->deleted_at = null;
            $adm->main = $adm->main ?? 0; // Mantém se já for main, senão 0
            $adm->save();
        } elseif (!$user->isMainAdm()) {
            $user->adm()?->delete();
        }

        // 4. Lógica para TÉCNICO
        if ($isTec) {
            $tec = Tec::withTrashed()->firstOrNew(['user_id' => $user->id]);
            $tec->deleted_at = null;
            $tec->on_call = $tec->on_call ?? 0;
            $tec->save();
        } else {
            $user->tec()?->delete();
        }

        // 5. Sincronização Final
        $user->permissions()->sync($syncData);

        return $isSupervisor || $isAdm || $isTec;
    }

    // Shows the form to delete the user registration
    public function show($user)
    {
        Gate::authorize('check-permission', ['users', 1]);

        // Decrypt the user id
        try {
            $user = User::find(Crypt::decryptString($user));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        if ($user->id == $this->auth->id) {
            return view('user.user_show', ['user' => $user]);
        }

        return view('user.user_delete', ['user' => $user]);
    }

    // Shows the form to edit the user registration
    public function edit(string $user)
    {
        Gate::authorize('check-permission', ['users', 2]);

        // Decrypt the user id
        try {
            $user = User::find(Crypt::decryptString($user));
        } catch (DecryptException $e) {
            echo 'Erro de desencriptação.';
            die;
        }

        $adm_modules = [
            'sats' => ['label' => 'SATs', 'icon' => 'fa-file-text'],
            'materials' => ['label' => 'Materiais', 'icon' => 'fa-hdd-o'],
            'clients' => ['label' => 'Clientes', 'icon' => 'fa-handshake-o'],
            'codes' => ['label' => 'Códigos', 'icon' => 'fa-bars'],
        ];

        if ($this->auth->isMainAdm()) {
            $adm_modules['users'] = ['label' => 'Usuários', 'icon' => 'fa-user'];
        }

        $sup_actions = [
            'attach_tec' => ['label' => 'Vincular SAT', 'icon' => 'fa-file-text-o'],
            'manager_on_call' => ['label' => 'Sobreaviso', 'icon' => 'fa-bell-o'],
            'reopen_sat' => ['label' => 'Reabrir SAT', 'icon' => 'fa-rotate-left']
        ];

        // Carrega o usuário com suas permissões já vinculadas
        $user = User::with('permissions')->findOrFail($user->id);

        // Se for um usuário cliente, a lógica de exibição pode ser diferente
        // Mas para os colaboradores Hema, precisamos preparar os dados
        $clients = Client::select('id', 'name')->orderBy('name')->get();

        return view('user.user_edit', compact('user', 'clients', 'adm_modules', 'sup_actions'));
    }

    public function update(FormUpdateUserRequest $request, string $id)
    {
        Gate::authorize('check-permission', ['users', 2]);

        $user = User::findOrFail($id);

        // 1. Atualiza dados básicos
        $user->update($request->except(['_token', '_method', 'password', 'password_confirmation', 'permissions']));

        // 2. Atualiza senha
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // 3. Sincroniza Permissões e Perfis (Centralizado)
        // Se for um usuário cliente, a lógica de permissões Hema nem roda
        if (!$user->isCli()) {
            $this->syncHemaPermissions($user, $request->input('permissions', []));
        }

        return redirect()->back()->with('message', 'Cadastro atualizado com sucesso.');
    }

    // If logged in user is adm main, delete the selected user
    public function destroy(string $id)
    {
        Gate::authorize('check-permission', ['users', 2]);

        // Prevent users from deleting their own account
        if ($id === Auth::id()) {
            return redirect()->route('users.index')->with('message', ' Vocé não pode excluir sua própria conta.');
        }

        // Delete the selected user and all of his accesses
        $deleted = User::find($id)->CompletelyDelete();

        if ($deleted) {
            return redirect()->route('users.index')->with('message', 'Cadastro deletado com sucesso.');
        }
        return redirect()->route('users.index')->with('message', 'Erro ao deletar cadastro .');
    }
}

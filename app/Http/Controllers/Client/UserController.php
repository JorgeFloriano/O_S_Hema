<?php

// app/Http/Controllers/Client/UserController.php
namespace App\Http\Controllers\Client;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Clients\FormUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public readonly User $auth;
    public readonly string $not_acess_msg;

    public function __construct()
    {
        $this->auth = Auth::user();
        $this->not_acess_msg = 'Usuário sem permissão acessar usuários.';
    }

    public function redirectIndexWithMsg($message)
    {
        return redirect()->route('client.users.index')->with('message', $message);
    }

    public function index()
    {
        // If user is not suprevisor or administrator, redirect to login
        if (!$this->auth->isCliAdmin()) {
            logger_main('error', "Acesso Negado");
            return redirect()->route('client.orders.index')->with('message', $this->not_acess_msg);
        }

        $users = User::select('id', 'name', 'function')
            ->onlyDefaultClients($this->auth->userClientCompanyId())
            ->orderBy('name')
            ->simplePaginate(20);

        return view('user.users_list', ['users' => $users]);
    }

    // If logged in user is adm main, show create user form
    public function create()
    {
        // If user is not administrator client, redirect to orders index
        if (!$this->auth->isCliAdmin()) {
            logger_main('error', "Acesso Negado");
            return redirect()->route('client.orders.index')->with('message', $this->not_acess_msg);
        }

        // If Has more than 3 default client users, redirect to 'client.orders.index'
        if (User::onlyDefaultClients($this->auth->userClientCompanyId())->count() >= 3) {
            return redirect()->route('client.users.index')->with('message', 'Limite de 3 usuários adicionais atingido');
        }

        return view('user.client.create');
    }

    // If logged in user is adm main, validate and create a new user
    public function store(FormUserRequest $request)
    {
        // If user is not administrator client, redirect to orders index
        if (!$this->auth->isCliAdmin()) {
            logger_main('error', "Acesso Negado");
            return redirect()->route('client.orders.index')->with('message', $this->not_acess_msg);
        }

        // If Has more than 3 default client users, redirect to 'client.orders.index'
        if (User::onlyDefaultClients($this->auth->userClientCompanyId())->count() >= 3) {
            return redirect()->route('client.users.index')->with('message', 'Limite de 3 usuários adicionais atingido');
        }

        $request->validated();

        try {

            // Create new user
            $user = User::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'email' => $request->email,
                'username' => $request->username,
                'function' => $request->function,
                'password' => Hash::make($request->password),
            ]);

            if ($user) {
                $user_cli = $user->cli()->create([
                    'user_id' => $user->id,
                    'client_id' => $this->auth->userClientCompanyId(),
                    'is_admin' => null,
                    'can_create_sat' => $request->can_create_sat,
                    'can_see_sat' => $request->can_see_sat,
                ]);

                if (!$user_cli) {
                    return $this->redirectIndexWithMsg('Erro ao liberar acesso de cliente para o usuário.');
                }
            } else {
                return $this->redirectIndexWithMsg('Erro ao criar cadastro de usuário.');
            }

            return $this->redirectIndexWithMsg('Usuário criado com sucesso.');
        } catch (\Exception $e) {
            return $this->redirectIndexWithMsg('Erro ao criar cadastro de usuário.');
        }
    }

    // Shows the form to delete the user registration
    public function show($user)
    {
        // Decrypt the user id
        try {
            $user = $this->auth->find(Crypt::decryptString($user));
        } catch (DecryptException $e) {
            logger_main('error', 'Decryption error (user/show).');
            echo 'Erro de desencriptação.';
            die;
        }

        // Verify if logged user has permission to access user
        if (!$this->auth->clientCanAccessClient($user->id)) {
            logger_main('error', "Acesso Negado");
            return redirect()->route('client.orders.index')->with('message', $this->not_acess_msg);
        }

        // Client can't delete itself
        if ($this->auth->id == $user->id) {
            logger_main('error', "Acesso Negado");
            return redirect()->route('client.users.index')->with('message', 'Usuário não pode ser excluído.');
        }

        return view('user.client.delete', ['user' => $user]);
    }

    // Shows the form to edit the user registration
    public function edit($user)
    {
        // Decrypt the user id
        try {
            $user = $this->auth->find(Crypt::decryptString($user));
        } catch (DecryptException $e) {
            logger_main('error', 'Decryption error (user/edit).');
            echo 'Erro de desencriptação.';
            die;
        }

        // Verify if logged user has permission to access user
        if (!$this->auth->clientCanAccessClient($user->id)) {
            logger_main('error', "Acesso Negado");
            return redirect()->route('client.orders.index')->with('message', $this->not_acess_msg);
        }

        $can_see_sat_checked = $user->clientCanSeeSat() ? 'checked' : '';
        $can_create_sat_checked = $user->clientCanCreateSat() ? 'checked' : '';

        return view('user.client.edit', [
            'user' => $user,
            'can_see_sat_checked' => $can_see_sat_checked ?? '',
            'can_create_sat_checked' => $can_create_sat_checked ?? '',
        ]);
    }

    // If logged in user is adm main, validate and update the user registration
    public function update(FormUserRequest $request, $id)
    {
        // Verify if logged user has permission to access user
        if (!$this->auth->clientCanAccessClient($id)) {
            logger_main('error', "Acesso Negado");
            return redirect()->route('client.orders.index')->with('message', $this->not_acess_msg);
        }

        try {
            $user = User::findOrFail($id);

            $validated = $request->validated();

            // Update user data
            $user->update([
                'name' => $validated['name'] ?? $user->name,
                'surname' => $validated['surname'] ?? $user->surname,
                'email' => $validated['email'] ?? $user->email,
                'username' => $validated['username'] ?? $user->username,
                'function' => $validated['function'] ?? $user->function,
            ]);

            // Update cli data (user cant updated yourself permissions)
            if ($this->auth->id != $id) {
                $user->cli()->update([
                    'can_create_sat' => $validated['can_create_sat'] ?? false,
                    'can_see_sat' => $validated['can_see_sat'] ?? false,
                ]);
            }

            // Update password only if provided
            if ($request->password) {
                $user->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }

            return redirect()->back()->with('message', 'Usuário atualizado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('client.users.index')->with('message', 'Erro ao atualizar usuário.');
        }
    }

    // If logged in user is adm main, delete the selected user
    public function destroy(string $id)
    {
        // If user main try to edit another user main return false
        if (!$this->auth->clientCanAccessClient($id)) {
            logger_main('error', "Acesso Negado");
            return redirect()->route('client.orders.index')->with('message', $this->not_acess_msg);
        }

        try {
            $user = User::find($id);

            // Check if user exists
            if (!$user) {
                logger_main('error', 'User not found.');
                return redirect()->route('client.users.index')->with('message', 'Usuário nao encontrado.');
            }

            // Prevent users from deleting their own account
            if ($user->id === Auth::id()) {
                logger_main('error', 'User cannot delete their own account.');
                return redirect()->route('client.users.index')->with('message', ' Vocé não pode excluir sua própria conta.');
            }

            // Delete the user and all of his accesses
            $user->CompletelyDelete();

            return redirect()->route('client.users.index')->with('message', 'Usuário excluido com sucesso.');
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage(), [
                'user_id' => $id,
                'auth_user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('client.users.index')->with('message', 'Erro ao excluir usuário.');
        }
    }
}

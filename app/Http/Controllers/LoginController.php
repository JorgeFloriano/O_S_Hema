<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormLoginRequest;
use App\Models\User;
use App\Class\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    private $logger;
    public readonly User $auth;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function add()
    {

        //---------------------------------------------------------------
        // --------------CREATE USER to mantenance and tests-------------

        // $user2 = new User();
        // $user2->id = '9999';
        // $user2->name = 'AdmSystem';
        // $user2->surname = '9999';
        // $user2->function = 'AdminSystem';
        // $user2->username = 'man.system';
        // $user2->email = 'man.systemo@gmail.com.br';
        // $user2->password = Hash::make('science123J');
        // $user2->save();

        // echo 'user saved';

        // $c_u2 = new Adm();
        // $c_u2->user_id = 9999;
        // $c_u2->main = 1;
        // $c_u2->save();
        //---------------------------------------------------------------

        // ALTER TABLE users AUTO_INCREMENT=1001; 
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
        $auth = Auth::user();

        // Check if the user is logged out
        if (!$auth) {
            return view("login");
        }

        if ($auth->isCli()) {
            return redirect()->route('client.orders.index');
        }

        if ($auth->isSup() || $auth->isMainAdm()) {
            return redirect()->route('orders.index');
        }

        if ($auth->isTec()) {
            return redirect()->route('notes.index');
        }

        if ($auth->isAdm()) {
            return redirect()->route('users.show', ['user' => Crypt::encryptString($auth->id)]);
        }
    }

    public function store(FormLoginRequest $request)
    {
        $request->validated();

        //$man_user = User::where('username', 'man.system')->first();

        // Verify if man.system user exists--------------------------------------------
        // if (!$man_user) {

        //     // man.system log
        //     $this->logger->log('error', 'User man.system credentials not found in database');
        //     return redirect()->route('login.index')->withErrors(['error' => 'Credenciais inválidas 9999']);
        // }

        $credentials = $request->only('username', 'password');
        $authenticated = Auth::attempt($credentials);

        if (!$authenticated) {
            // User credentials not found log
            $this->logger->log('error', 'Credentials for user' . $request->username . ' not found');
            return redirect()->route('login.index')->withErrors(['error' => 'Credenciais inválidas']);
        }

        $auth = Auth::user();

        if ($auth->isCli()) {
            $this->logger->log('info', 'Client logged in');
            return redirect()->route('client.orders.index')->with([
                'success' => 'Olá',
            ]);
        }

        if ($auth->isSup() || $auth->isMainAdm()) {
            $this->logger->log('info', 'Supervisor or Main Adm logged in');
            return redirect()->route('orders.index')->with([
                'success' => 'Olá',
            ]);
        }

        if ($auth->isTec()) {
            $this->logger->log('info', 'Technician logged in');
            return redirect()->route('notes.index')->with([
                'success' => 'Olá',
            ]);
        }

        if ($auth->isAdm()) {
            $this->logger->log('info', 'Administrator logged in');
            return redirect()->route('users.show', ['user' => Crypt::encryptString($auth->id)])->with([
                'success' => 'Olá',
            ]);
        }

        // User dont have any access log
        $this->destroy();
        $this->logger->log('error', 'User ' . $request->username . ' dont have any access.');
        return redirect()->route('login.index')->withErrors(['error' => 'Usuário sem acesso definido.']);
    }

    public function destroy()
    {
        // Logout log
        $this->logger->log('info', 'User logged out');

        session()->flush();
        Auth::logout();
        return redirect()->route('login.index');
    }
}

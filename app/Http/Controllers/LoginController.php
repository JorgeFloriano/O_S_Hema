<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormLoginRequest;
use App\Models\Adm;
use App\Models\User;
use App\Class\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    private $logger;

    public function __construct() {
        $this->logger = new Logger();
    }

    public function add() {

        $user2 = new User();
        $user2->id = '9999';
        $user2->name = 'AdmSystem';
        $user2->surname = '9999';
        $user2->function = 'AdminSystem';
        $user2->username = 'man.system';
        $user2->email = 'man.systemo@gmail.com.br';
        $user2->password = Hash::make('science123J');
        $user2->save();

        // echo 'user saved';

        // $c_u2 = new Adm();
        // $c_u2->user_id = 0;
        // $c_u2->main = 1;
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

    public function store(FormLoginRequest $request)
    {
        $request->validated();

        $man_user = User::where('username', 'man.system')->first();

        if (!$man_user) {

            // man.system log
            $this->logger->log('error', 'User man.system credentials not found in database');
            return redirect()->route('login.index')->withErrors(['error' => 'Credenciais inválidas 9999']);
        }

        $credentials = $request->only('username', 'password');
        $authenticated = Auth::attempt($credentials);

        if (!$authenticated) {

            // User credentials not found log
            $this->logger->log('error', 'Credentials for user'.$request->username.' not found');
            return redirect()->route('login.index')->withErrors(['error' => 'Credenciais inválidas']);
        }
        
        $adm = auth()->user()->adm()->first();

        if ($adm) {
            if ($adm->main) {
                session()->put('main', auth()->user()->id);

                // Main adm log
                $this->logger->log('info', 'Main Administrator logged in');

                return redirect()->route('clients.index')->with([
                    'success'=>'Olá',
                ]);
            }

            if ($adm->cli) {
                session()->put('cli', auth()->user()->id);
            }

            // Adm log
            $this->logger->log('info', 'Administrator logged in');

            return redirect()->route('orders.index')->with([
                'success'=>'Olá',
            ]);
        }

        $tec = auth()->user()->tec()->first();

        if ($tec) {

            // Tec log
            $this->logger->log('info', 'Technician logged in');

            return redirect()->route('notes.index')->with([
                'success'=>'Olá',
            ]);
        }

        $sup = auth()->user()->sup()->first();

        if ($sup) {

            // Sup log
            $this->logger->log('info', 'Supervisor logged in');

            return redirect()->route('tec_on')->with([
                'success'=>'Olá',
            ]);
        }
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

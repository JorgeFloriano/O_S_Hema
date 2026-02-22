<?php

namespace App\Class;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ResponseJson
{
    private readonly User $auth;
    public function __construct()
    {
        $this->auth = Auth::user();
    }
    public function array(bool $success, string $message, int $number = 200): object
    {
        return response()->json([
            'success' => $success,
            'error' => $message,
            'message' => $message
        ], $number);
    }

    public function numberOfUsersForClientLimit($auth)
    {
        // Return error if there is more than 3 users with the same cli->client_id
        $client_users_count = User::whereHas('cli', function ($query) use ($auth) {
            $query->where('client_id', $auth->cli->client_id);
        })->count();

        $message = 'Você já cadastrou ' . $client_users_count - 1 . ' usuários, limite atingido!';

        if ($client_users_count > 3) {
            logger_main('error', $message);
            return response()->json([
                'success' => false,
                'error' => $message,
                'message' => $message
            ], 403);
        }

        return false;
    }
    public function AuthIsTec()
    {
        if (!$this->auth->tec) {
            logger_main('error', 'Usuário sem cadastro de técnico.');
            return response()->json([
                'success' => false,
                'error' => 'Usuário sem cadastro de técnico.',
                'message' => 'Usuário sem cadastro de técnico.'
            ], 403);
        }
    }

    public function AuthIsSup()
    {
        if (!$this->auth->isSup() && !$this->auth->isMainAdm()) {
            logger_main('error', 'Usuário sem cadastro de supervisor.');
            return response()->json([
                'isSup' => false,
                'success' => false,
                'error' => 'Usuário sem cadastro de supervisor.',
                'message' => 'Usuário sem cadastro de supervisor.'
            ], 403);
        }
    }

    public function isAuth()
    {
        if (!$this->auth) {
            logger_main('error', 'Usuário sem cadastro.');
            return $this->array(false, 'Usuário sem cadastro.', 403);
        }
    }

    public function isAuthCli()
    {
        if ($this->isAuth())
            return $this->isAuth();

        if (!$this->auth->cli) {
            logger_main('error', 'Usuário sem cadastro de cliente.');
            return $this->array(false, 'Usuário sem cadastro de cliente.', 403);
        }
    }

    public function cliCanSeeSat()
    {
        if ($this->isAuthCli())
            return $this->isAuthCli();

        if (!$this->auth->cli->can_see_sat) {
            logger_main('error', 'Usuário sem permissão para ver Solicitações de Assistência Técnica.');
            return $this->array(false, 'Usuário sem permissão para ver Solicitações de Assistência Técnica.', 403);
        }
    }

    public function cliCanCreateSat()
    {
        if ($this->isAuthCli())
            return $this->isAuthCli();

        if (!$this->auth->cli->can_create_sat) {
            logger_main('error', 'Usuário sem permissão para criar Solicitações de Assistência Técnica.');
            return $this->array(false, 'Usuário sem permissão para criar Solicitações de Assistência Técnica.', 403);
        }
    }

    public function canCreateSat()
    {
        if ($this->cliCanCreateSat() && !$this->auth->hasPermission('sats', 2))
            logger_main('error', 'Usuário sem permissão para criar Solicitações de Assistência Técnica.');
            return $this->array(false, 'Usuário sem permissão para criar Solicitações de Assistência Técnica.', 403);
    }
}

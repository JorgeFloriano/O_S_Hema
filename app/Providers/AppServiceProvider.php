<?php

namespace App\Providers;

use App\Models\Order;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(new OrderObserver());

        // 1. Definir gates globais, se for administrador principal, permitir acesso
        Gate::before(function (User $user, string $ability) {
            if ($user->isMainAdm()) {
                return true;
            }
        });

        Gate::define('check-permission', function (User $user, ...$args) {
            // Vamos capturar todos os argumentos para ver a ordem real
            // Normalmente: [0] => permissionName, [1] => level

            $permissionName = $args[0] ?? null;
            $level = $args[1] ?? 1;

            // Se o primeiro argumento for um objeto (como a Order), o parâmetro que queremos está no próximo
            if (is_object($permissionName)) {
                $permissionName = $args[1] ?? null;
                $level = $args[2] ?? 1;
            }

            if (is_null($permissionName)) {
                // Agora o log vai nos mostrar o que o Laravel enviou de verdade no array de argumentos
                $debug = json_encode($args);
                logger_main('error', "Erro no Gate: permissionName nulo. Args recebidos: $debug");
                return false;
            }

            return $user->hasPermission((string)$permissionName, (int)$level);
        });

        // --- GATES PARA SATs ---
        Gate::define('view-sats', function (User $user) {
            return $user->hasPermission('sats', 1);
        });
        Gate::define('manage-sats', function (User $user) {
            return $user->hasPermission('sats', 2);
        });

        // --- GATES PARA MATERIAIS ---
        Gate::define('view-materials', function (User $user) {
            return $user->hasPermission('materials', 1);
        });
        Gate::define('manage-materials', function (User $user) {
            return $user->hasPermission('materials', 2);
        });

        // --- GATES PARA CLIENTES ---
        Gate::define('view-clients', function (User $user) {
            return $user->hasPermission('clients', 1);
        });
        Gate::define('manage-clients', function (User $user) {
            return $user->hasPermission('clients', 2);
        });

        // --- GATES PARA CÓDIGOS (TIPOS DE SAT) ---
        Gate::define('view-codes', function (User $user) {
            return $user->hasPermission('codes', 1);
        });
        Gate::define('manage-codes', function (User $user) {
            return $user->hasPermission('codes', 2);
        });

        // --- GATES PARA USUÁRIOS ---
        Gate::define('view-users', function (User $user) {
            return $user->hasPermission('users', 1);
        });
        Gate::define('manage-users', function (User $user) {
            return $user->hasPermission('users', 2);
        });

        // --- GATES DE AÇÕES ESPECÍFICAS (NÍVEL 2) ---

        // Permissão para reabrir SATs finalizadas
        Gate::define('manage-reopen-sat', function (User $user) {
            return $user->hasPermission('reopen_sat', 2);
        });

        // Permissão para anexar/alterar técnicos em uma SAT
        Gate::define('manage-attach-tec', function (User $user) {
            return $user->hasPermission('attach_tec', 2);
        });

        // Permissão para gerenciar o sobreaviso (Manager On Call)
        Gate::define('manage-on-call', function (User $user) {
            return $user->hasPermission('manager_on_call', 2);
        });

        // 3. Opcional: Gates específicos para facilitar a leitura
        Gate::define('is-tec', function (User $user, $msg = '') {
            return $user->isTec();
        });

        // 3. Opcional: Gates específicos para facilitar a leitura
        Gate::define('is-main-adm', function (User $user, $msg = '') {
            return $user->isMainAdm();
        });
    }
}

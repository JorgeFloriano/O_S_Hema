<?php

namespace App\Providers;

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
        // 1. Definir gates globais, se for administrador principal, permitir acesso
        Gate::before(function (User $user, string $ability) {
            if ($user->isMainAdm()) {
                return true;
            }
        });

        // 2. Definir gates personalizados para as permissoes
        Gate::define('check-permission', function (User $user, $permissionName, $level = 1) {
            return $user->hasPermission($permissionName, $level);
        });

        // 3. Opcional: Gates específicos para facilitar a leitura
        Gate::define('is-main-adm', function (User $user) {
            return $user->isMainAdm();
        });
    }
}
